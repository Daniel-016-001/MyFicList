<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;

class AnimeSearchService
{
    /**
     * Busca anime en cascada: BD -> TMDB -> Jikan
     * Devuelve un único resultado
     */
    public function search(string $query, string $type = 'anime'): ?array
    {
        // Buscar en TMDB primero
        $tmdbResult = $this->searchInTmdb($query, $type);
        if ($tmdbResult) {
            return $this->translateData($tmdbResult);
        }

        // Luego buscar en Jikan con traducción
        $jikanResult = $this->searchInJikan($query, $type);
        if ($jikanResult) {
            return $this->translateData($jikanResult);
        }

        return null;
    }

    /**
     * Busca múltiples resultados en cascada: BD -> APIs
     * Devuelve un array de resultados (máximo 10)
     */
    public function searchMultiple(string $query, string $type = 'anime'): array
    {
        $results = [];

        try {
            // 1. Buscar en BD local primero
            $dbResults = $this->searchInDatabase($query, $type);
            $results = array_merge($results, $dbResults);

            // 2. Buscar en TMDB si es anime/manga (que es TV)
            if (in_array($type, ['anime', 'manga'])) {
                $tmdbResults = $this->searchMultipleInTmdb($query, $type);
                $results = array_merge($results, $tmdbResults);
            }

            // 3. Buscar en Jikan para anime/manga
            if (in_array($type, ['anime', 'manga'])) {
                $jikanResults = $this->searchMultipleInJikan($query, $type);
                $results = array_merge($results, $jikanResults);
            }

            // 4. Para movies/series buscar en TMDB
            if (in_array($type, ['movie', 'series'])) {
                $tmdbResults = $this->searchMultipleInTmdb($query, $type);
                $results = array_merge($results, $tmdbResults);
            }

            // 5. Para games buscar en RAWG
            if ($type === 'game') {
                $rawgResults = $this->searchMultipleInRawg($query);
                $results = array_merge($results, $rawgResults);
            }
        } catch (\Exception $e) {
            // Return whatever we found so far
        }

        // Limitar a 10 resultados máximo y eliminar duplicados
        $unique = [];
        $seen = [];
        
        foreach ($results as $result) {
            $key = $result['source'] . ':' . $result['external_id'];
            if (!isset($seen[$key]) && count($unique) < 10) {
                $seen[$key] = true;
                $unique[] = $result;
            }
        }

        return $unique;
    }

    /**
     * Busca en la base de datos local
     */
    private function searchInDatabase(string $query, string $type): array
    {
        try {
            $dbResults = \App\Models\Media::where('title', 'LIKE', "%{$query}%")
                ->where('media_type', $type)
                ->limit(3)
                ->get()
                ->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'external_id' => $media->external_id,
                        'title' => $media->title,
                        'cover_url' => $media->cover_url,
                        'synopsis' => substr($media->synopsis, 0, 150) . '...',
                        'source' => 'Local',
                        'is_stored' => true
                    ];
                })
                ->toArray();

            return $dbResults;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Busca múltiples resultados en TMDB
     */
    private function searchMultipleInTmdb(string $query, string $type): array
    {
        try {
            $tmdbType = ($type == 'movie') ? 'movie' : 'tv';
            $response = Http::withToken(config('services.tmdb.token'))
                ->get("https://api.themoviedb.org/3/search/{$tmdbType}", [
                    'query' => $query,
                    'language' => 'es-ES'
                ]);

            $results = $response->json()['results'] ?? [];
            
            return array_map(function ($item) {
                return [
                    'id' => null, // No está guardado localmente
                    'external_id' => $item['id'],
                    'title' => $item['title'] ?? $item['name'],
                    'cover_url' => $item['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path'] : null,
                    'synopsis' => $item['overview'] ? substr($item['overview'], 0, 150) . '...' : 'Sin descripción',
                    'source' => 'TMDB',
                    'is_stored' => false,
                    'media_type' => ($item['media_type'] ?? 'tv') === 'movie' ? 'movie' : 'series'
                ];
            }, array_slice($results, 0, 4));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Busca múltiples resultados en Jikan
     */
    private function searchMultipleInJikan(string $query, string $type): array
    {
        try {
            $endpoint = ($type == 'manga') ? 'manga' : 'anime';
            $response = Http::get("https://api.jikan.moe/v4/{$endpoint}", [
                'q' => $query,
                'limit' => 5
            ]);

            $results = $response->json()['data'] ?? [];

            return array_map(function ($item) use ($type) {
                return [
                    'id' => null,
                    'external_id' => $item['mal_id'],
                    'title' => $this->translateText($item['title']),
                    'cover_url' => $item['images']['jpg']['large_image_url'],
                    'synopsis' => $this->translateText(substr($item['synopsis'] ?? '', 0, 150)) . '...',
                    'source' => 'Jikan',
                    'is_stored' => false,
                    'media_type' => $type
                ];
            }, array_slice($results, 0, 4));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Busca múltiples resultados en RAWG
     */
    private function searchMultipleInRawg(string $query): array
    {
        try {
            $response = Http::get("https://api.rawg.io/api/games", [
                'key' => config('services.rawg.key'),
                'search' => $query,
                'page_size' => 4
            ]);

            $results = $response->json()['results'] ?? [];

            return array_map(function ($item) {
                return [
                    'id' => null,
                    'external_id' => $item['id'],
                    'title' => $item['name'],
                    'cover_url' => $item['background_image'],
                    'synopsis' => substr($item['description'] ?? 'Sin descripción', 0, 150) . '...',
                    'source' => 'RAWG',
                    'is_stored' => false,
                    'media_type' => 'game'
                ];
            }, array_slice($results, 0, 4));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Busca en TMDB (resultado único)
     */
    private function searchInTmdb(string $query, string $type): ?array
    {
        try {
            $response = Http::withToken(config('services.tmdb.token'))
                ->get("https://api.themoviedb.org/3/search/tv", [
                    'query' => $query,
                    'language' => 'es-ES'
                ]);

            $basic = $response->json()['results'][0] ?? null;

            if (!$basic) {
                return null;
            }

            $details = Http::withToken(config('services.tmdb.token'))
                ->get("https://api.themoviedb.org/3/tv/{$basic['id']}", [
                    'append_to_response' => 'videos',
                    'language' => 'es-ES'
                ])->json();

            $trailer = collect($details['videos']['results'] ?? [])
                ->where('type', 'Trailer')
                ->first();

            // Si no hay sinopsis en español, obtener la versión en inglés
            $synopsis = $details['overview'] ?? $basic['overview'] ?? '';

            return [
                'external_id' => $basic['id'],
                'title' => $basic['name'],
                'cover_url' => 'https://image.tmdb.org/t/p/w500' . $basic['poster_path'],
                'synopsis' => $synopsis,
                'source' => 'TMDB',
                'extra_data' => [
                    'backdrop' => 'https://image.tmdb.org/t/p/original' . ($basic['backdrop_path'] ?? ''),
                    'trailer_url' => $trailer ? 'https://www.youtube.com/embed/' . $trailer['key'] : null,
                    'rating' => $basic['vote_average'] ?? 'N/A',
                    'release_date' => $basic['first_air_date'] ?? 'N/A',
                    'status' => $basic['status'] ?? 'N/A'
                ]
            ];
        } catch (\Exception $e) {
            // Logging removed for compatibility
            return null;
        }
    }

    /**
     * Busca en Jikan (MyAnimeList API)
     */
    private function searchInJikan(string $query, string $type): ?array
    {
        try {
            $endpoint = ($type == 'manga') ? 'manga' : 'anime';
            $response = Http::get("https://api.jikan.moe/v4/{$endpoint}", [
                'q' => $query,
                'limit' => 1
            ]);

            $item = $response->json()['data'][0] ?? null;

            if (!$item) {
                return null;
            }

            return [
                'external_id' => $item['mal_id'],
                'title' => $item['title'],
                'title_english' => $item['title_english'] ?? null,
                'title_japanese' => $item['title_japanese'] ?? null,
                'cover_url' => $item['images']['jpg']['large_image_url'],
                'synopsis' => $item['synopsis'],
                'source' => 'Jikan',
                'extra_data' => [
                    'trailer_url' => ($type == 'anime') ? ($item['trailer']['embed_url'] ?? null) : null,
                    'score' => $item['score'] ?? 'N/A',
                    'status' => $item['status'],
                    'chapters' => $item['chapters'] ?? null,
                    'volumes' => $item['volumes'] ?? null,
                    'type' => $item['type'] ?? null,
                    'episodes' => $item['episodes'] ?? null,
                    'backdrop' => null
                ]
            ];
        } catch (\Exception $e) {
            // Logging removed for compatibility
            return null;
        }
    }

    /**
     * Traduce los datos al español
     */
    private function translateData(array $data): array
    {
        try {
            // Traducir título si es necesario
            if (!empty($data['title'])) {
                $data['title'] = $this->translateText($data['title']);
            }

            // Traducir sinopsis
            if (!empty($data['synopsis'])) {
                $data['synopsis'] = $this->translateText($data['synopsis']);
            }
        } catch (\Exception $e) {
            // Translation error - return original data
        }

        return $data;
    }

    /**
     * Traduce un texto al español usando Google Translate
     */
    private function translateText(string $text): string
    {
        try {
            // Evitar traducir si es null o muy corto
            if (empty($text) || strlen($text) < 3) {
                return $text;
            }

            $translator = new GoogleTranslate();
            $translator->setSource('auto');
            $translator->setTarget('es');
            
            $translated = $translator->translate($text);
            
            // Si la traducción falla o devuelve el mismo texto, devolver original
            return !empty($translated) ? $translated : $text;
        } catch (\Exception $e) {
            // Translation error - return original text
            return $text;
        }
    }
}
