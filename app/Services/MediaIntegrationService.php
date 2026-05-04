<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Media;

class MediaIntegrationService
{
    /**
     * Obtiene resultados unificados de múltiples fuentes
     */
    public function getUnifiedResults(string $query = ''): array
    {
        if (empty($query))
            return [];

        $allResults = [];
        $types = ['anime', 'manga', 'movie', 'series', 'game'];

        foreach ($types as $type) {
            try {
                // searchByType ya debería estar usando el Cache que pusimos en AnimeSearchService
                $results = $this->searchByType($query, $type);

                // Limitamos a 3 resultados por categoría para que la mezcla sea equilibrada
                $topResults = array_slice($results, 0, 3);

                foreach ($topResults as $result) {
                    // Aseguramos que la vista sepa qué tipo de media es
                    $result['media_type'] = $result['media_type'] ?? $type;
                    $allResults[] = $result;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error buscando $type: " . $e->getMessage());
            }
        }

        // Opcional: Barajar los resultados para que no salgan siempre primero los animes
        // shuffle($allResults); 

        return $allResults;
    }

    /**
     * Lógica central de importación a la base de datos
     */
    public function importToDatabase($externalId, $source, $type): ?Media
    {
        $details = match ($source) {
            'Jikan' => $this->getJikanDetails($externalId, $type),
            'TMDB' => $this->getTmdbDetails($externalId, $type),
            'RAWG' => $this->getRawgDetails($externalId),
            default => null,
        };

        if (!$details) {
            return null;
        }

        $dataToSave = [
            'external_id' => $details['external_id'],
            'title' => $details['title'],
            'cover_url' => $details['cover_url'] ?? null,
            'synopsis' => $details['synopsis'] ?? '',
            'extra_data' => [
                'score' => $details['score'] ?? null,
                'trailer_url' => $details['trailer_url'] ?? null,
                'images' => $details['images'] ?? [],
            ],
        ];

        $dataToSave['media_type'] = $type;
        $dataToSave['source'] = $source;

        return Media::create($dataToSave);
    }

    /**
     * Obtiene detalles completos de un contenido desde APIs externas
     */
    public function getExternalDetails($externalId, $source, $type): ?array
    {
        try {
            return match ($source) {
                'Jikan' => $this->getJikanDetails($externalId, $type),
                'TMDB' => $this->getTmdbDetails($externalId, $type),
                'RAWG' => $this->getRawgDetails($externalId),
                'OpenLibrary' => $this->getOpenLibraryDetails($externalId),
                default => null,
            };
        } catch (\Exception $e) {
            \Log::error("Error obteniendo detalles externos: " . $e->getMessage());
            return null;
        }
    }

    private function getJikanDetails($id, $type): array
    {
        $endpoint = ($type === 'manga') ? 'manga' : 'anime';
        $item = Http::timeout(5)->get("https://api.jikan.moe/v4/{$endpoint}/{$id}")->json()['data'];

        $tr = new GoogleTranslate('es');
        $trailer = $item['trailer']['url'] ?? null;
        $images = [];

        if (!empty($item['images']['jpg'])) {
            foreach ($item['images']['jpg'] as $image) {
                if (is_string($image)) {
                    $images[] = $image;
                }
            }
        }

        return [
            'id' => $item['mal_id'],
            'title' => $item['title'],
            'original_title' => $item['title_japanese'] ?? $item['title'],
            'cover_url' => $item['images']['jpg']['large_image_url'] ?? null,
            'synopsis' => $tr->translate($item['synopsis'] ?? ''),
            'type' => ucfirst($type),
            'source' => 'Jikan',
            'external_id' => $item['mal_id'],
            'status' => $item['status'] ?? 'Unknown',
            'episodes' => $item['episodes'] ?? null,
            'chapters' => $item['chapters'] ?? null,
            'volumes' => $item['volumes'] ?? null,
            'score' => $item['score'] ?? null,
            'genres' => collect($item['genres'] ?? [])->pluck('name')->toArray(),
            'studios' => collect($item['studios'] ?? [])->pluck('name')->toArray(),
            'authors' => collect($item['authors'] ?? [])->pluck('name')->toArray(),
            'year' => $item['year'] ?? null,
            'trailer_url' => $trailer,
            'images' => array_values(array_unique(array_filter($images))),
        ];
    }

    private function getTmdbDetails($id, $type): array
    {
        $tmdbType = ($type === 'movie') ? 'movie' : 'tv';
        $details = Http::withToken(config('services.tmdb.token'))
            ->get("https://api.themoviedb.org/3/{$tmdbType}/{$id}", [
                'language' => 'es-ES',
                'append_to_response' => 'videos,images'
            ])
            ->json();

        $video = collect($details['videos']['results'] ?? [])
            ->firstWhere('type', 'Trailer');

        if (!$video) {
            $video = collect($details['videos']['results'] ?? [])
                ->firstWhere('type', 'Teaser');
        }

        $trailerUrl = $video ? "https://www.youtube.com/watch?v={$video['key']}" : null;
        $images = [];

        foreach (array_slice($details['images']['backdrops'] ?? [], 0, 4) as $backdrop) {
            if (!empty($backdrop['file_path'])) {
                $images[] = 'https://image.tmdb.org/t/p/w780' . $backdrop['file_path'];
            }
        }

        foreach (array_slice($details['images']['posters'] ?? [], 0, 4) as $poster) {
            if (!empty($poster['file_path'])) {
                $images[] = 'https://image.tmdb.org/t/p/w500' . $poster['file_path'];
            }
        }

        return [
            'id' => $details['id'],
            'title' => $details['title'] ?? $details['name'],
            'original_title' => $details['original_title'] ?? $details['original_name'] ?? ($details['title'] ?? $details['name']),
            'cover_url' => !empty($details['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $details['poster_path'] : (!empty($details['backdrop_path']) ? 'https://image.tmdb.org/t/p/w780' . $details['backdrop_path'] : null),
            'synopsis' => $this->translateText($details['overview'] ?? ''),
            'type' => ucfirst($type),
            'source' => 'TMDB',
            'external_id' => $details['id'],
            'status' => $details['status'] ?? null,
            'episodes' => $details['number_of_episodes'] ?? null,
            'chapters' => null,
            'volumes' => null,
            'score' => $details['vote_average'] ?? null,
            'genres' => collect($details['genres'] ?? [])->pluck('name')->toArray(),
            'studios' => collect($details['production_companies'] ?? [])->pluck('name')->toArray(),
            'authors' => collect($details['created_by'] ?? [])->pluck('name')->toArray(),
            'year' => substr($details['release_date'] ?? $details['first_air_date'] ?? '', 0, 4),
            'trailer_url' => $trailerUrl,
            'images' => array_values(array_unique(array_filter($images))),
        ];
    }

    private function getRawgDetails($id): array
    {
        $response = Http::get("https://api.rawg.io/api/games/{$id}", [
            'key' => config('services.rawg.key'),
        ]);

        $details = $response->json();

        return [
            'id' => $details['id'],
            'title' => $details['name'],
            'original_title' => $details['name'],
            'cover_url' => $details['background_image'],
            'synopsis' => $this->translateText($details['description_raw'] ?? ''),
            'type' => 'Juego',
            'source' => 'RAWG',
            'external_id' => $details['id'],
            'status' => null,
            'episodes' => null,
            'chapters' => null,
            'volumes' => null,
            'score' => $details['rating'] ?? null,
            'genres' => collect($details['genres'] ?? [])->pluck('name')->toArray(),
            'studios' => collect($details['developers'] ?? [])->pluck('name')->toArray(),
            'authors' => [],
            'year' => substr($details['released'] ?? '', 0, 4),
            'trailer_url' => null,
            'images' => [],
        ];
    }

    private function getOpenLibraryDetails($id): array
    {
        $response = Http::get("https://openlibrary.org/works/{$id}.json");
        $details = $response->json();
        $description = $details['description'] ?? '';

        if (is_array($description)) {
            $description = $description['value'] ?? '';
        }

        return [
            'id' => $id,
            'title' => $details['title'] ?? 'Título desconocido',
            'original_title' => $details['title'] ?? 'Título desconocido',
            'cover_url' => isset($details['covers']) ? "https://covers.openlibrary.org/b/olid/{$id}-L.jpg" : null,
            'synopsis' => $this->translateText($description),
            'type' => 'Libro',
            'source' => 'OpenLibrary',
            'external_id' => $id,
            'status' => null,
            'episodes' => null,
            'chapters' => null,
            'volumes' => null,
            'score' => null,
            'genres' => $details['subjects'] ?? [],
            'studios' => [],
            'authors' => collect($details['authors'] ?? [])->map(function($author) {
                return $author['author']['key'] ?? '';
            })->toArray(),
            'year' => substr($details['first_publish_date'] ?? '', 0, 4),
            'trailer_url' => null,
            'images' => [],
        ];
    }

    private function fetchRawg($id)
    {
        $details = Http::get("https://api.rawg.io/api/games/{$id}", [
            'key' => config('services.rawg.key')
        ])->json();

        return [
            'external_id' => $id,
            'title' => $details['name'],
            'cover_url' => $details['background_image'],
            'synopsis' => $this->translateText(strip_tags($details['description'] ?? '')),
            'extra_data' => ['metacritic' => $details['metacritic'] ?? 'N/A']
        ];
    }

    private function translateText(string $text): string
    {
        $text = trim($text);

        if (empty($text)) {
            return '';
        }

        try {
            $translator = new GoogleTranslate('es');
            return $translator->translate($text);
        } catch (\Exception $e) {
            Log::warning('No se pudo traducir texto: ' . $e->getMessage());
            return $text;
        }
    }

    // Nota: El método searchByType debería llamar a AnimeSearchService o 
    // contener la lógica de búsqueda rápida que tenías antes.
    private function searchByType($query, $type)
    {
        // Aquí conectas con tu AnimeSearchService actual
        return app(\App\Services\AnimeSearchService::class)->searchMultiple($query, $type);
    }
}