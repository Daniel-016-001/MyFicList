<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Media;

class SearchService
{
    /**
     * Busca múltiples resultados en cascada con CACHE
     * Este es el método principal que llama el Controller
     */
    public function searchMultiple(string $query, string $type = 'anime'): array
    {
        $safe = request()->filled('safe');
        $cacheKey = "search_{$type}_" . md5($query) . ($safe ? '_nsfw' : '_safe');

        return Cache::remember($cacheKey, 1800, function () use ($query, $type, $safe) {
            $results = [];

            $localResults = $this->searchInDatabase($query, $type, false, $safe);
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
                if ($type === 'manga') {
                    // El Manga SOLO viene de Jikan (TMDB no tiene manga)
                    $jikanResults = array_filter($this->searchMultipleInJikan($query, $type, $safe), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                    $results = array_merge($results, $jikanResults);
                } elseif ($type === 'anime') {
                    // El Anime viene de Jikan y de TMDB (filtrando por animación)
                    $jikanResults = array_filter($this->searchMultipleInJikan($query, $type, $safe), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                    $results = array_merge($results, $jikanResults);

                    $tmdbResults = array_filter($this->searchMultipleInTmdb($query, $type, $safe), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
                    $results = array_merge($results, $tmdbResults);
                } elseif (in_array($type, ['movie', 'series', 'peli', 'serie'])) {
                    // Películas y Series vienen de TMDB
                    $tmdbResults = array_filter($this->searchMultipleInTmdb($query, $type, $safe), fn($item) => !$this->isExistingSearchResult($item, $existingKeys, $existingTitles));
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

        return $unique->take(20)->toArray();
    }

    private function isExistingSearchResult(array $item, array $existingKeys, array $existingTitles): bool
    {
        // Comprobar coincidencia exacta por (source + external_id)
        $key = trim(($item['source'] ?? '') . '_' . ($item['external_id'] ?? ''));
        if ($key !== '' && in_array($key, $existingKeys, true)) {
            return true;
        }

        // También comprobar solo por external_id para evitar duplicados entre fuentes distintas
        // (ej. un registro guardado como RAWG_9767 vs resultado de búsqueda RAWG_9767)
        if (!empty($item['external_id'])) {
            $externalIdOnly = (string) $item['external_id'];
            foreach ($existingKeys as $existingKey) {
                if (str_ends_with($existingKey, '_' . $externalIdOnly)) {
                    return true;
                }
            }
        }

        if (!empty($item['title']) && in_array($this->normalizeString($item['title']), $existingTitles, true)) {
            return true;
        }

        return false;
    }

    // --- MÉTODOS DE BÚSQUEDA ESPECÍFICOS ---

    private function searchInDatabase(string $query, string $type, bool $exact = false, bool $showAdult = false): array
    {
        $builder = Media::where('media_type', $type);

        if (!$showAdult) {
            // Filtrar contenido adulto si la casilla NO está marcada
            $builder->where(function ($q) {
                $q->whereNull('extra_data->is_adult')
                  ->orWhere('extra_data->is_adult', false);
            })->where(function ($q) {
                $q->whereNull('extra_data->rating')
                  ->orWhere('extra_data->rating', 'NOT LIKE', '%hentai%');
            });
        }

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
                'source' => $m->source, // Usar la fuente real del registro, no sobreescribir con 'Local'
                'is_stored' => true,
                'media_type' => $type
            ])->toArray();
    }

    private function searchMultipleInTmdb(string $query, string $type, bool $showAdult = false): array
    {
        try {
            // Si buscamos anime, TMDB no tiene categoría propia, así que usamos búsqueda MULTI
            // para encontrar tanto películas como series de animación
            if ($type === 'anime') {
                $response = Http::withToken(config('services.tmdb.token'))
                    ->get("https://api.themoviedb.org/3/search/multi", [
                        'query' => $query,
                        'language' => 'es-ES',
                        'include_adult' => $showAdult
                    ]);
                
                $results = $response->json()['results'] ?? [];
                
                // Filtrar por género Animación (16)
                $results = array_filter($results, function($item) {
                    return (($item['media_type'] ?? '') === 'movie' || ($item['media_type'] ?? '') === 'tv') 
                           && in_array(16, $item['genre_ids'] ?? []);
                });

            } else {
                $tmdbType = ($type == 'movie' || $type == 'peli') ? 'movie' : 'tv';
                $response = Http::withToken(config('services.tmdb.token'))
                    ->get("https://api.themoviedb.org/3/search/{$tmdbType}", [
                        'query' => $query,
                        'language' => 'es-ES',
                        'include_adult' => $showAdult
                    ]);

                $results = $response->json()['results'] ?? [];

                // Filtrar para EXCLUIR animaciones si buscamos series/películas de imagen real
                $results = array_filter($results, fn($item) => !in_array(16, $item['genre_ids'] ?? []));
            }

            return array_map(function ($item) use ($type) {
                // En búsqueda multi de TMDB, el tipo real viene en media_type
                $actualType = $type;
                if ($type === 'anime' && isset($item['media_type'])) {
                    $actualType = ($item['media_type'] === 'movie') ? 'peli' : 'serie';
                    // Pero mantenemos la categoría visual como 'anime' para que se guarde correctamente
                    // en la sección de anime del usuario
                    $actualType = 'anime';
                }

                if ($actualType === 'movie') $actualType = 'peli';
                if ($actualType === 'series') $actualType = 'serie';

                return [
                    'id' => null,
                    'external_id' => $item['id'],
                    'title' => $item['title'] ?? $item['name'],
                    'cover_url' => $item['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path'] : null,
                    'synopsis' => $item['overview'] ? substr($item['overview'], 0, 120) . '...' : 'Sin descripción',
                    'source' => 'TMDB',
                    'is_stored' => false,
                    'media_type' => $actualType
                ];
            }, array_slice($results, 0, 8));
        } catch (\Exception $e) {
            return [];
        }
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

    private function searchMultipleInJikan(string $query, string $type, bool $showAdult = false): array
    {
        try {
            $endpoint = ($type == 'manga') ? 'manga' : 'anime';
            $response = Http::get("https://api.jikan.moe/v4/{$endpoint}", [
                'q' => $query,
                'limit' => 10,
                'sfw' => !$showAdult
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
                    'media_type' => ($item['type'] === 'Movie') ? 'peli' : $type
                ];
            }, array_slice($results, 0, 8));
        } catch (\Exception $e) {
            return [];
        }
    }

    // ... (Puedes mantener tus métodos de RAWG y OpenLibrary igual que antes)

    private function translateText(string $text): string
    {
        try {
            if (empty($text) || strlen($text) < 3)
                return $text;
            $translator = new GoogleTranslate('es');
            return $translator->translate($text);
        } catch (\Exception $e) {
            return $text;
        }
    }

    private function searchMultipleInRawg(string $query): array
    {
        try {
            $response = Http::get("https://api.rawg.io/api/games", [
                'key' => config('services.rawg.key'),
                'search' => $query,
                'page_size' => 10
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
        } catch (\Exception $e) {
            return [];
        }
    }

    private function searchMultipleInOpenLibrary(string $query): array
    {
        try {
            $response = Http::get("https://openlibrary.org/search.json", [
                'q' => $query,
                'limit' => 10
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
        } catch (\Exception $e) {
            return [];
        }
    }
}