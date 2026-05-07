<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Media;

class AnimeSearchService
{
    /**
     * Busca múltiples resultados en cascada con CACHE
     * Este es el método principal que llama el Controller
     */
    public function searchMultiple(string $query, string $type = 'anime'): array
    {
        $cacheKey = "search_{$type}_" . md5($query);
        
        return Cache::remember($cacheKey, 1800, function() use ($query, $type) {
            $results = [];

            $localResults = $this->searchInDatabase($query, $type);
            $exactLocalResults = $this->searchInDatabase($query, $type, true);

            if (!empty($exactLocalResults)) {
                return $exactLocalResults;
            }

            $results = array_merge($results, $localResults);

            $existingKeys = collect($localResults)
                ->map(fn($item) => trim(($item['source'] ?? '') . '_' . ($item['external_id'] ?? '')))
                ->filter()
                ->toArray();

            $existingTitles = collect($localResults)
                ->map(fn($item) => $this->normalizeString($item['title'] ?? ''))
                ->filter()
                ->toArray();

            try {
                if (in_array($type, ['anime', 'manga'])) {
                    $tmdbResults = array_filter($this->searchMultipleInTmdb($query, $type), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                    $results = array_merge($results, $tmdbResults);

                    if ($type === 'manga' || count($tmdbResults) === 0 || !$this->isExactTmdbMatch($query, $tmdbResults)) {
                        $jikanResults = array_filter($this->searchMultipleInJikan($query, $type), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                        $results = array_merge($results, $jikanResults);
                    }
                } elseif (in_array($type, ['movie', 'series'])) {
                    $tmdbResults = array_filter($this->searchMultipleInTmdb($query, $type), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                    $results = array_merge($results, $tmdbResults);
                } elseif ($type === 'game') {
                    $rawgResults = array_filter($this->searchMultipleInRawg($query), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                    $results = array_merge($results, $rawgResults);
                } elseif ($type === 'book') {
                    $openLibraryResults = array_filter($this->searchMultipleInOpenLibrary($query), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                    $results = array_merge($results, $openLibraryResults);
                }
            } catch (\Exception $e) {
                // Si falla una API (ej. Jikan tiene rate limit), no rompemos la app
            }

            return $this->formatAndFilter($results);
        });
    }

    /**
     * Limpieza de duplicados y formato para la vista
     */
    private function formatAndFilter(array $results): array
    {
        $collection = collect($results);

        // Agrupar por título normalizado para detectar duplicados
        $grouped = $collection->groupBy(function ($item) {
            return strtolower(trim($item['title']));
        });

        $unique = $grouped->map(function ($group) {
            // Si hay múltiples resultados con el mismo título, preferir:
            // 1. Local (si_stored = true)
            // 2. TMDB
            // 3. Jikan
            // 4. Otros
            
            $local = $group->firstWhere('is_stored', true);
            if ($local) {
                return $local;
            }

            $tmdb = $group->firstWhere('source', 'TMDB');
            if ($tmdb) {
                return $tmdb;
            }

            $jikan = $group->firstWhere('source', 'Jikan');
            if ($jikan) {
                return $jikan;
            }

            // Si no hay ninguna preferencia, devolver el primero
            return $group->first();
        })->values();

        return $unique->take(12)->toArray();
    }

    private function isExistingSearchResult(array $item, array $existingKeys, array $existingTitles): bool
    {
        $key = trim(($item['source'] ?? '') . '_' . ($item['external_id'] ?? ''));

        if ($key !== '' && in_array($key, $existingKeys, true)) {
            return true;
        }

        if (!empty($item['title']) && in_array($this->normalizeString($item['title']), $existingTitles, true)) {
            return true;
        }

        return false;
    }

    // --- MÉTODOS DE BÚSQUEDA ESPECÍFICOS ---

    private function searchInDatabase(string $query, string $type, bool $exact = false): array
    {
        $builder = Media::where('media_type', $type);

        if ($exact) {
            $builder->whereRaw('LOWER(title) = ?', [mb_strtolower(trim($query), 'UTF-8')]);
        } else {
            $builder->where('title', 'LIKE', "%{$query}%");
        }

        return $builder->limit(4)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'external_id' => $m->external_id,
                'title' => $m->title,
                'cover_url' => $m->cover_url,
                'synopsis' => substr($m->synopsis, 0, 120) . '...',
                'source' => 'Local',
                'is_stored' => true,
                'media_type' => $type
            ])->toArray();
    }

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
            
            // Filtrar animaciones si estamos buscando series
            if ($type === 'series') {
                $results = array_filter($results, fn($item) => !in_array(16, $item['genre_ids'] ?? []));
            }
            
            return array_map(function ($item) use ($type) {
                return [
                    'id' => null,
                    'external_id' => $item['id'],
                    'title' => $item['title'] ?? $item['name'],
                    'cover_url' => $item['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path'] : null,
                    'synopsis' => $item['overview'] ? substr($item['overview'], 0, 120) . '...' : 'Sin descripción',
                    'source' => 'TMDB',
                    'is_stored' => false,
                    'media_type' => $type 
                ];
            }, array_slice($results, 0, 4));
        } catch (\Exception $e) { return []; }
    }

    private function isExactTmdbMatch(string $query, array $tmdbResults): bool
    {
        $normalizedQuery = $this->normalizeString($query);

        foreach ($tmdbResults as $result) {
            $title = $result['title'] ?? '';
            if ($this->normalizeString($title) === $normalizedQuery) {
                return true;
            }
        }

        return false;
    }

    private function normalizeString(string $text): string
    {
        return mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/u', '', trim($text)), 'UTF-8');
    }

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
                    'title' => $item['title'],
                    'cover_url' => $item['images']['jpg']['large_image_url'],
                    'synopsis' => $this->translateText(substr($item['synopsis'] ?? '', 0, 120)) . '...',
                    'source' => 'Jikan',
                    'is_stored' => false,
                    'media_type' => $type
                ];
            }, array_slice($results, 0, 4));
        } catch (\Exception $e) { return []; }
    }

    // ... (Puedes mantener tus métodos de RAWG y OpenLibrary igual que antes)

    private function translateText(string $text): string
    {
        try {
            if (empty($text) || strlen($text) < 3) return $text;
            $translator = new GoogleTranslate('es');
            return $translator->translate($text);
        } catch (\Exception $e) { return $text; }
    }

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
                    'cover_url' => $item['background_image'] ?? null,
                    'synopsis' => $this->translateText(substr($item['description'] ?? '', 0, 120)) . '...',
                    'source' => 'RAWG',
                    'is_stored' => false,
                    'media_type' => 'game'
                ];
            }, $results);
        } catch (\Exception $e) { return []; }
    }

    private function searchMultipleInOpenLibrary(string $query): array
    {
        try {
            $response = Http::get("https://openlibrary.org/search.json", [
                'q' => $query,
                'limit' => 4
            ]);

            $results = $response->json()['docs'] ?? [];

            return array_map(function ($item) {
                return [
                    'id' => null,
                    'external_id' => $item['key'],
                    'title' => $item['title'],
                    'cover_url' => isset($item['cover_i']) ? "https://covers.openlibrary.org/b/id/{$item['cover_i']}-L.jpg" : null,
                    'synopsis' => $this->translateText(substr($item['first_sentence'] ?? '', 0, 120)) . '...',
                    'source' => 'OpenLibrary',
                    'is_stored' => false,
                    'media_type' => 'book'
                ];
            }, $results);
        } catch (\Exception $e) { return []; }
    }
}