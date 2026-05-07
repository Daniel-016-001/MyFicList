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

        $details['media_type'] = $type;

        return $this->importSearchResult($details);
    }

    public function importSearchResult(array $result): ?Media
    {
        if (empty($result['external_id']) || empty($result['source'])) {
            return null;
        }

        $mediaType = $result['media_type'] ?? strtolower($result['type'] ?? '');
        $mediaType = match (strtolower($mediaType)) {
            'libro' => 'book',
            'juego' => 'game',
            'pelicula', 'películas' => 'movie',
            'serie' => 'series',
            default => strtolower($mediaType),
        };

        if ($result['source'] === 'TMDB' && $this->hasAnimationCategory($result['categories'] ?? $result['genres'] ?? [])) {
            $mediaType = 'anime';
        }

        if (empty($mediaType)) {
            return null;
        }

        $title = trim($result['title'] ?? '');
        if ($title !== '') {
            $existingByTitle = Media::where('media_type', $mediaType)
                ->whereRaw('LOWER(title) = ?', [mb_strtolower($title, 'UTF-8')])
                ->first();

            if ($existingByTitle) {
                return $existingByTitle;
            }
        }

        $needsDetails = empty($result['genres']) || empty($result['categories']) || (!array_key_exists('episodes', $result) && !array_key_exists('chapters', $result));
        if ($result['source'] !== 'Local' && $needsDetails) {
            $details = $this->getExternalDetails($result['external_id'], $result['source'], $mediaType);
            if (!empty($details)) {
                $result = array_merge($result, $details);
            }
        }

        $media = Media::firstOrNew([
            'external_id' => $result['external_id'],
            'source' => $result['source'],
        ]);

        $media->title = $result['title'] ?? $media->title;
        $media->media_type = $mediaType;
        $media->cover_url = $result['cover_url'] ?? $media->cover_url;
        $media->synopsis = $result['synopsis'] ?? $media->synopsis;

        $extraData = $media->extra_data ?? [];
        unset($extraData['score']);

        $media->extra_data = array_merge(
            $extraData,
            [
                'trailer_url' => $result['trailer_url'] ?? data_get($result, 'trailer_url'),
                'images'      => $result['images'] ?? data_get($result, 'images', []),
                'year'        => $result['year'] ?? data_get($result, 'year'),
                'genres'      => $result['genres'] ?? data_get($result, 'genres', []),
                'categories'  => $result['categories'] ?? data_get($result, 'categories', []),
                'episodes'    => $result['episodes'] ?? data_get($result, 'episodes'),
                'chapters'    => $result['chapters'] ?? data_get($result, 'chapters'),
                // No guardamos la puntuación de la plataforma
            ]
        );

        $media->save();

        return $media;
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

    private function hasAnimationCategory(array $categories): bool
    {
        return collect($categories)
            ->filter()
            ->map(fn($category) => mb_strtolower($category, 'UTF-8'))
            ->contains(fn($category) => str_contains($category, 'animación') || str_contains($category, 'animation'));
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

        $genres = collect($item['genres'] ?? [])->pluck('name')->toArray();

        return [
            'external_id'    => $item['mal_id'],
            'title'          => $item['title'],
            'original_title' => $item['title_japanese'] ?? $item['title'],
            'cover_url'      => $item['images']['jpg']['large_image_url'] ?? null,
            'synopsis'       => $this->translateText($item['synopsis'] ?? ''),
            'type'           => ucfirst($type),
            'source'         => 'Jikan',
            'genres'         => $genres,
            'categories'     => $genres,
            'year'           => $item['year'] ?? null,
            'trailer_url'    => $item['trailer']['url'] ?? null,
            'images'         => array_values(array_unique(array_filter($images))),
            'episodes'       => $item['episodes'] ?? null,
            'chapters'       => $item['chapters'] ?? null,
            'studios'        => collect($item['studios'] ?? [])->pluck('name')->toArray(),
            'authors'        => collect($item['authors'] ?? [])->pluck('name')->toArray(),
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

        $genres = collect($details['genres'] ?? [])->pluck('name')->toArray();

        return [
            'external_id'    => $details['id'],
            'title'          => $details['title'] ?? $details['name'],
            'original_title' => $details['original_title'] ?? $details['original_name'] ?? $details['title'],
            'cover_url'      => $details['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $details['poster_path'] : null,
            'synopsis'       => $details['overview'] ?: $this->translateText($details['overview'] ?? ''),
            'type'           => ucfirst($type),
            'source'         => 'TMDB',
            'genres'         => $genres,
            'categories'     => $genres,
            'year'           => substr($details['release_date'] ?? $details['first_air_date'] ?? '', 0, 4),
            'trailer_url'    => $video ? "https://www.youtube.com/watch?v={$video['key']}" : null,
            'images'         => array_values(array_unique(array_filter($images))),
            'episodes'       => $details['number_of_episodes'] ?? null,
            'chapters'       => null,
            'studios'        => collect($details['production_companies'] ?? [])->pluck('name')->toArray(),
            'authors'        => [],
        ];
    }

    private function getRawgDetails($id): array
    {
        $details = Http::get("https://api.rawg.io/api/games/{$id}", [
            'key' => config('services.rawg.key'),
        ])->json();

        $genres = collect($details['genres'] ?? [])->pluck('name')->toArray();

        return [
            'external_id' => $details['id'],
            'title'       => $details['name'],
            'cover_url'   => $details['background_image'],
            'synopsis'    => $this->translateText($details['description_raw'] ?? ''),
            'type'        => 'Juego',
            'source'      => 'RAWG',
            'genres'      => $genres,
            'categories'  => $genres,
            'year'        => substr($details['released'] ?? '', 0, 4),
            'images'      => [],
            'episodes'    => null,
            'chapters'    => null,
            'studios'     => collect($details['developers'] ?? [])->pluck('name')->toArray(),
            'authors'     => collect($details['publishers'] ?? [])->pluck('name')->toArray(),
        ];
    }

    private function getOpenLibraryDetails($id): array
    {
        $details = Http::get("https://openlibrary.org/works/{$id}.json")->json();
        $description = is_array($details['description'] ?? '') 
            ? ($details['description']['value'] ?? '') 
            : ($details['description'] ?? '');

        $subjects = is_array($details['subjects'] ?? null) ? $details['subjects'] : [];

        return [
            'external_id' => $id,
            'title'       => $details['title'] ?? 'Título desconocido',
            'cover_url'   => isset($details['covers']) ? "https://covers.openlibrary.org/b/olid/{$id}-L.jpg" : null,
            'synopsis'    => $this->translateText($description),
            'type'        => 'Libro',
            'source'      => 'OpenLibrary',
            'genres'      => $subjects,
            'categories'  => $subjects,
            'year'        => substr($details['first_publish_date'] ?? '', 0, 4),
            'images'      => [],
            'episodes'    => null,
            'chapters'    => $details['number_of_pages'] ?? null,
            'studios'     => [],
            'authors'     => collect($details['authors'] ?? [])->pluck('name')->toArray(),
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