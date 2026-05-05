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
     * Corregido para manejar animación occidental y evitar duplicados de Anime.
     */
    public function getUnifiedResults(string $query = ''): array
    {
        if (empty($query)) return [];

        $allResults = [];
        $types = ['anime', 'manga', 'movie', 'series', 'game'];
        
        // Registro de títulos para evitar duplicados entre APIs
        $foundTitles = []; 

        foreach ($types as $type) {
            try {
                $results = $this->searchByType($query, $type);
                $topResults = array_slice($results, 0, 3);

                foreach ($topResults as $result) {
                    $titleKey = strtolower(trim($result['title']));
                    
                    // LÓGICA DE FILTRADO INTELIGENTE
                    if ($result['source'] === 'TMDB') {
                        // El ID de género 16 en TMDB es "Animación"
                        $isAnimation = in_array(16, $result['genre_ids'] ?? []);
                        
                        // Si es animación de TMDB, solo la añadimos si NO la hemos encontrado ya en Jikan
                        // Esto permite que pase "Código Lyoko" pero no "Naruto" (que ya vendrá por Jikan)
                        if ($isAnimation && in_array($titleKey, $foundTitles)) {
                            continue; 
                        }
                    }

                    // Si es de Jikan, guardamos el título para bloquear duplicados de TMDB después
                    if ($result['source'] === 'Jikan') {
                        $foundTitles[] = $titleKey;
                    }

                    $result['media_type'] = $result['media_type'] ?? $type;
                    $allResults[] = $result;
                }
            } catch (\Exception $e) {
                Log::error("Error buscando $type: " . $e->getMessage());
            }
        }

        return $allResults;
    }

    /**
     * Lógica central de importación a la base de datos
     */
    public function importToDatabase($externalId, $source, $type): ?Media
    {
        $details = $this->getExternalDetails($externalId, $source, $type);

        if (!$details) {
            return null;
        }

        $dataToSave = [
            'external_id' => $details['external_id'],
            'title'       => $details['title'],
            'cover_url'   => $details['cover_url'] ?? null,
            'synopsis'    => $details['synopsis'] ?? '',
            'media_type'  => $type,
            'source'      => $source,
            'extra_data'  => [
                'score'       => $details['score'] ?? null,
                'trailer_url' => $details['trailer_url'] ?? null,
                'images'      => $details['images'] ?? [],
                'year'        => $details['year'] ?? null,
                'genres'      => $details['genres'] ?? [],
            ],
        ];

        return Media::create($dataToSave);
    }

    /**
     * Obtiene detalles completos y TRADUCIDOS de un contenido
     */
    public function getExternalDetails($externalId, $source, $type): ?array
    {
        try {
            return match ($source) {
                'Jikan'       => $this->getJikanDetails($externalId, $type),
                'TMDB'        => $this->getTmdbDetails($externalId, $type),
                'RAWG'        => $this->getRawgDetails($externalId),
                'OpenLibrary' => $this->getOpenLibraryDetails($externalId),
                default       => null,
            };
        } catch (\Exception $e) {
            Log::error("Error obteniendo detalles externos ($source): " . $e->getMessage());
            return null;
        }
    }

    private function getJikanDetails($id, $type): array
    {
        $endpoint = ($type === 'manga') ? 'manga' : 'anime';
        $response = Http::timeout(5)->get("https://api.jikan.moe/v4/{$endpoint}/{$id}");
        $item = $response->json()['data'];

        $images = [];
        if (!empty($item['images']['jpg'])) {
            foreach ($item['images']['jpg'] as $image) {
                if (is_string($image)) $images[] = $image;
            }
        }

        return [
            'external_id'    => $item['mal_id'],
            'title'          => $item['title'],
            'original_title' => $item['title_japanese'] ?? $item['title'],
            'cover_url'      => $item['images']['jpg']['large_image_url'] ?? null,
            'synopsis'       => $this->translateText($item['synopsis'] ?? ''),
            'type'           => ucfirst($type),
            'source'         => 'Jikan',
            'score'          => $item['score'] ?? null,
            'genres'         => collect($item['genres'] ?? [])->pluck('name')->toArray(),
            'year'           => $item['year'] ?? null,
            'trailer_url'    => $item['trailer']['url'] ?? null,
            'images'         => array_values(array_unique(array_filter($images))),
        ];
    }

    private function getTmdbDetails($id, $type): array
    {
        $tmdbType = ($type === 'movie') ? 'movie' : 'tv';
        $details = Http::withToken(config('services.tmdb.token'))
            ->get("https://api.themoviedb.org/3/{$tmdbType}/{$id}", [
                'language' => 'es-ES',
                'append_to_response' => 'videos,images'
            ])->json();

        $video = collect($details['videos']['results'] ?? [])->firstWhere('type', 'Trailer') 
                 ?? collect($details['videos']['results'] ?? [])->firstWhere('type', 'Teaser');

        $images = [];
        foreach (array_slice($details['images']['backdrops'] ?? [], 0, 4) as $img) {
            $images[] = 'https://image.tmdb.org/t/p/w780' . $img['file_path'];
        }

        return [
            'external_id'    => $details['id'],
            'title'          => $details['title'] ?? $details['name'],
            'original_title' => $details['original_title'] ?? $details['original_name'] ?? $details['title'],
            'cover_url'      => $details['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $details['poster_path'] : null,
            'synopsis'       => $details['overview'] ?: $this->translateText($details['overview'] ?? ''),
            'type'           => ucfirst($type),
            'source'         => 'TMDB',
            'score'          => $details['vote_average'] ?? null,
            'genres'         => collect($details['genres'] ?? [])->pluck('name')->toArray(),
            'year'           => substr($details['release_date'] ?? $details['first_air_date'] ?? '', 0, 4),
            'trailer_url'    => $video ? "https://www.youtube.com/watch?v={$video['key']}" : null,
            'images'         => array_values(array_unique(array_filter($images))),
        ];
    }

    private function getRawgDetails($id): array
    {
        $details = Http::get("https://api.rawg.io/api/games/{$id}", [
            'key' => config('services.rawg.key'),
        ])->json();

        return [
            'external_id' => $details['id'],
            'title'       => $details['name'],
            'cover_url'   => $details['background_image'],
            'synopsis'    => $this->translateText($details['description_raw'] ?? ''),
            'type'        => 'Juego',
            'source'      => 'RAWG',
            'score'       => $details['rating'] ?? null,
            'genres'      => collect($details['genres'] ?? [])->pluck('name')->toArray(),
            'year'        => substr($details['released'] ?? '', 0, 4),
            'images'      => [],
        ];
    }

    private function getOpenLibraryDetails($id): array
    {
        $details = Http::get("https://openlibrary.org/works/{$id}.json")->json();
        $description = is_array($details['description'] ?? '') 
            ? ($details['description']['value'] ?? '') 
            : ($details['description'] ?? '');

        return [
            'external_id' => $id,
            'title'       => $details['title'] ?? 'Título desconocido',
            'cover_url'   => isset($details['covers']) ? "https://covers.openlibrary.org/b/olid/{$id}-L.jpg" : null,
            'synopsis'    => $this->translateText($description),
            'type'        => 'Libro',
            'source'      => 'OpenLibrary',
            'genres'      => $details['subjects'] ?? [],
            'year'        => substr($details['first_publish_date'] ?? '', 0, 4),
        ];
    }

    /**
     * Helper de traducción (se mantiene tal cual pediste)
     */
    private function translateText(string $text): string
    {
        $text = trim($text);
        if (empty($text)) return '';

        try {
            $translator = new GoogleTranslate('es');
            return $translator->translate($text);
        } catch (\Exception $e) {
            Log::warning('No se pudo traducir texto: ' . $e->getMessage());
            return $text;
        }
    }

    private function searchByType($query, $type)
    {
        return app(\App\Services\AnimeSearchService::class)->searchMultiple($query, $type);
    }
}