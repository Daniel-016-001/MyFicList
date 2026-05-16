<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Media;
use App\Models\MediaList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MediaIntegrationService
{
    /**
     * Sincroniza o importa un resultado de búsqueda a la base de datos
     */
    public function importSearchResult(array $result, bool $isFullDetail = false): ?Media
    {
        $media = Media::where('source', $result['source'])
            ->where('external_id', $result['external_id'])
            ->first();

        // FILTRO ANTI-NSFW: Solo bloqueamos si el usuario NO ha marcado la casilla "+18"
        // Si request('safe') está presente, significa que el usuario QUIERE ver contenido +18
        $showAdult = request()->filled('safe');

        if (!$showAdult) {
            if (!empty($result['is_adult']) && $result['is_adult'] === true) {
                Log::warning("Contenido NSFW bloqueado (TMDB): " . ($result['title'] ?? 'ID ' . $result['external_id']));
                return null;
            }

            if (!empty($result['rating']) && str_contains(strtolower($result['rating']), 'hentai')) {
                Log::warning("Contenido NSFW bloqueado (Jikan): " . ($result['title'] ?? 'ID ' . $result['external_id']));
                return null;
            }
        }

        if (!$media) {
            $media = new Media();
            $media->source = $result['source'];
            $media->external_id = $result['external_id'];
        }

        if (!empty($result['title']))
            $media->title = $result['title'];
        if (!empty($result['cover_url']))
            $media->cover_url = $result['cover_url'];

        $newSynopsis = trim($result['synopsis'] ?? '');
        // Si la sinopsis es solo "..." o está vacía, la ignoramos si ya tenemos algo
        $isPoorSynopsis = ($newSynopsis === '...' || $newSynopsis === '');

        if ($isFullDetail) {
            // Si es la carga completa de detalles, mandamos nosotros
            if (!$isPoorSynopsis)
                $media->synopsis = $newSynopsis;
        } else {
            // Si es una búsqueda, solo guardamos si no había nada
            if (!$media->synopsis && !$isPoorSynopsis) {
                $media->synopsis = $newSynopsis;
            }
        }

        if (!empty($result['media_type'])) {
            $media->media_type = $result['media_type'];
        }

        if (isset($result['episodes_count'])) {
            $media->episodes_count = $result['episodes_count'];
        }
        if (isset($result['episode_duration'])) {
            $media->episode_duration = $result['episode_duration'];
        }
        if (isset($result['total_duration'])) {
            $media->total_duration = $result['total_duration'];
        }

        // GESTIÓN DE DETALLES
        $existingExtra = $media->extra_data ?? [];
        $newExtra = [
            'trailer_url' => $result['trailer_url'] ?? null,
            'year' => $result['year'] ?? null,
            'genres' => $result['genres'] ?? [],
            'categories' => $result['categories'] ?? [],
            'episodes' => $result['episodes'] ?? null,
            'seasons' => $result['seasons'] ?? null,
            'chapters' => $result['chapters'] ?? null,
            'studios' => $result['studios'] ?? [],
            'authors' => $result['authors'] ?? [],
            'platforms' => $result['platforms'] ?? [],
            'is_adult' => $result['is_adult'] ?? false,
            'rating' => $result['rating'] ?? null,
        ];

        if ($isFullDetail) {
            // En carga completa, los nuevos datos mandan (pero no borramos lo que no venga en la API)
            $merged = array_merge($existingExtra, array_filter($newExtra, fn($v) => !is_null($v) && $v !== '' && $v !== []));
            $merged['full_details_loaded'] = true;
            $media->extra_data = $merged;
        } else {
            // En búsqueda, solo añadimos lo que falte
            foreach ($newExtra as $key => $value) {
                if (!isset($existingExtra[$key]) || empty($existingExtra[$key])) {
                    if (!is_null($value) && $value !== '' && $value !== []) {
                        $existingExtra[$key] = $value;
                    }
                }
            }
            $media->extra_data = $existingExtra;
        }

        $media->save();

        return $media;
    }

    /**
     * Importa un contenido directamente desde su ID externo obteniendo detalles completos
     */
    public function importToDatabase($externalId, $source, $type): ?Media
    {
        $details = $this->getExternalDetails($externalId, $source, $type);

        if (!$details)
            return null;

        return $this->importSearchResult($details, true);
    }

    /**
     * Obtiene resultados unificados de varias fuentes para una búsqueda global
     */
    public function getUnifiedResults(string $query): array
    {
        $SearchService = app(SearchService::class);
        $results = [];

        $types = ['anime', 'manga', 'peli', 'serie', 'game', 'book'];

        foreach ($types as $type) {
            try {
                $results = array_merge($results, $SearchService->searchMultiple($query, $type));
            } catch (\Exception $e) {
                Log::warning("Fallo en búsqueda unificada para tipo {$type}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Obtiene detalles completos desde la API externa
     */
    public function getExternalDetails($id, $source, $type): ?array
    {
        try {
            switch ($source) {
                case 'TMDB':
                    return $this->getTmdbDetails($id, $type);
                case 'Jikan':
                    return $this->getJikanDetails($id, $type);
                case 'RAWG':
                    return $this->getRawgDetails($id);
                case 'OpenLibrary':
                    return $this->getOpenLibraryDetails($id);
            }
        } catch (\Exception $e) {
            Log::error("Error obteniendo detalles externos ({$source}): " . $e->getMessage());
        }

        return null;
    }

    private function getRawgDetails($id): ?array
    {
        try {
            // 1. Obtener detalles básicos
            $response = Http::get("https://api.rawg.io/api/games/{$id}", [
                'key' => config('services.rawg.key')
            ]);

            if ($response->failed()) {
                Log::warning("RAWG API request failed for ID {$id}: " . $response->status());
                return null;
            }

            $details = $response->json();

            if (empty($details) || isset($details['detail'])) {
                Log::warning("RAWG Game details not found for ID {$id}: " . ($details['detail'] ?? 'Empty response'));
                return null;
            }

            // 2. Obtener trailers/videos (Opcional, no debe romper si falla)
            $trailerUrl = null;
            try {
                $moviesResponse = Http::get("https://api.rawg.io/api/games/{$id}/movies", [
                    'key' => config('services.rawg.key')
                ]);

                if ($moviesResponse->successful()) {
                    $movies = $moviesResponse->json();
                    $trailerUrl = $movies['results'][0]['data']['max'] ?? $movies['results'][0]['data']['480'] ?? null;
                }

                // Si no hay movie, intentar fallback YouTube (Solo si el anterior falló o no existe)
                if (!$trailerUrl) {
                    $youtubeResponse = Http::get("https://api.rawg.io/api/games/{$id}/youtube", [
                        'key' => config('services.rawg.key')
                    ]);

                    if ($youtubeResponse->successful()) {
                        $youtube = $youtubeResponse->json();
                        if (isset($youtube['results'][0]['external_id'])) {
                            $trailerUrl = "https://www.youtube.com/watch?v=" . $youtube['results'][0]['external_id'];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Error fetching RAWG media for ID {$id}: " . $e->getMessage());
            }

            $genres = collect($details['genres'] ?? [])->map(fn($g) => $this->translateText($g['name']))->toArray();
            $platforms = collect($details['platforms'] ?? [])->pluck('platform.name')->toArray();

            return [
                'external_id' => $id,
                'title' => $details['name'] ?? 'Sin título',
                'cover_url' => $details['background_image'] ?? null,
                'synopsis' => $this->translateText($details['description_raw'] ?? strip_tags($details['description'] ?? '')),
                'media_type' => 'game',
                'source' => 'RAWG',
                'genres' => $genres,
                'categories' => $genres,
                'year' => isset($details['released']) ? substr($details['released'], 0, 4) : null,
                'trailer_url' => $trailerUrl,
                'platforms' => $platforms,
                'images' => [],
                'episodes' => null,
                'episodes_count' => null,
                'episode_duration' => null,
                'total_duration' => $details['playtime'] ?? null,
                'chapters' => null,
                'studios' => collect($details['developers'] ?? [])->pluck('name')->toArray(),
                'authors' => collect($details['publishers'] ?? [])->pluck('name')->toArray(),
                'media_type' => 'game'
            ];
        } catch (\Exception $e) {
            Log::error("Critical error in getRawgDetails for ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    private function getTmdbDetails($id, $type): array
    {
        $tmdbType = ($type == 'movie' || $type == 'peli') ? 'movie' : 'tv';
        $details = Http::withToken(config('services.tmdb.token'))
            ->get("https://api.themoviedb.org/3/{$tmdbType}/{$id}", [
                'language' => 'es-ES',
                'append_to_response' => 'videos,credits'
            ])->json();

        $trailer = collect($details['videos']['results'] ?? [])->firstWhere('type', 'Trailer');
        $trailerUrl = $trailer ? "https://www.youtube.com/watch?v={$trailer['key']}" : null;
        $genres = collect($details['genres'] ?? [])->pluck('name')->toArray();

        // Para series, los "autores" son los creadores
        $authors = ($type == 'series')
            ? collect($details['created_by'] ?? [])->pluck('name')->toArray()
            : collect($details['credits']['crew'] ?? [])->where('job', 'Director')->pluck('name')->toArray();

        $episodesCount = $details['number_of_episodes'] ?? null;
        $totalDuration = ($type == 'movie' || $type == 'peli') ? ($details['runtime'] ?? null) : null;
        $episodeDuration = null;
        
        if ($type == 'series' || $type == 'serie') {
            $runtimes = $details['episode_run_time'] ?? [];
            if (!empty($runtimes)) {
                $episodeDuration = $runtimes[0] . ' min';
            }
        }

        return [
            'external_id' => $details['id'],
            'title' => $details['title'] ?? $details['name'],
            'cover_url' => $details['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $details['poster_path'] : null,
            'synopsis' => $details['overview'],
            'type' => ($type == 'movie' || $type == 'peli') ? 'Película' : 'Serie',
            'source' => 'TMDB',
            'genres' => $genres,
            'categories' => $genres,
            'year' => substr($details['release_date'] ?? $details['first_air_date'] ?? '', 0, 4),
            'trailer_url' => $trailerUrl,
            'images' => [],
            'episodes' => $episodesCount,
            'episodes_count' => $episodesCount,
            'episode_duration' => $episodeDuration,
            'total_duration' => $totalDuration,
            'seasons' => $details['number_of_seasons'] ?? null,
            'chapters' => null,
            'studios' => collect($details['production_companies'] ?? [])->pluck('name')->toArray(),
            'authors' => $authors,
            'is_adult' => $details['adult'] ?? false,
            'media_type' => ($type == 'movie' || $type == 'peli') ? 'peli' : (($type == 'series' || $type == 'serie') ? 'serie' : $type)
        ];
    }

    private function getJikanDetails($id, $type): array
    {
        $endpoint = ($type == 'manga') ? 'manga' : 'anime';
        $details = Http::get("https://api.jikan.moe/v4/{$endpoint}/{$id}/full")->json()['data'] ?? [];

        $episodesCount = $details['episodes'] ?? null;
        $episodeDuration = $details['duration'] ?? null;
        $totalDuration = null;
        
        // Si es una película, intentamos extraer los minutos del string de duración de Jikan (ej: "1 hr 45 min")
        if ($details['type'] === 'Movie' && $episodeDuration) {
            if (preg_match('/(\d+)\s*hr/', $episodeDuration, $matchesHr)) {
                $totalDuration += intval($matchesHr[1]) * 60;
            }
            if (preg_match('/(\d+)\s*min/', $episodeDuration, $matchesMin)) {
                $totalDuration += intval($matchesMin[1]);
            }
        }

        return [
            'external_id' => $details['mal_id'],
            'title' => $details['title'],
            'cover_url' => $details['images']['jpg']['large_image_url'],
            'synopsis' => $this->translateText($details['synopsis'] ?? ''),
            'type' => ($type == 'manga') ? 'Manga' : (($details['type'] === 'Movie') ? 'Película' : 'Anime'),
            'source' => 'Jikan',
            'genres' => collect($details['genres'] ?? [])->pluck('name')->toArray(),
            'categories' => collect($details['genres'] ?? [])->pluck('name')->toArray(),
            'year' => $details['year'] ?? substr($details['published']['from'] ?? '', 0, 4),
            'trailer_url' => $details['trailer']['url'] ?? null,
            'images' => [],
            'episodes' => $episodesCount,
            'episodes_count' => $episodesCount,
            'episode_duration' => $episodeDuration,
            'total_duration' => $totalDuration,
            'chapters' => $details['chapters'] ?? $details['volumes'] ?? null,
            'studios' => collect($details['studios'] ?? [])->pluck('name')->toArray(),
            'authors' => collect($details['authors'] ?? [])->pluck('name')->toArray(),
            'rating' => $details['rating'] ?? '',
            'media_type' => ($details['type'] === 'Movie') ? 'peli' : $type
        ];
    }

    private function getOpenLibraryDetails($id): array
    {
        $cleanId = str_replace('/works/', '', $id);
        $details = Http::get("https://openlibrary.org/works/{$cleanId}.json")->json();

        if (empty($details) || !isset($details['title'])) {
            return [];
        }

        // Obtener descripción (puede ser string o array)
        $description = $details['description'] ?? '';
        if (is_array($description)) {
            $description = $description['value'] ?? '';
        }
        
        // Limpiar "cosas raras" de OpenLibrary (enlaces markdown y separadores)
        // Convierte [Texto](url) en simplemente "Texto"
        $description = preg_replace('/\[([^\]]+)\]\s*\([^\)]+\)/', '$1', $description);
        // Elimina líneas separadoras largas (------)
        $description = preg_replace('/-{5,}/', '', $description);

        // Obtener géneros (subjects)
        $genres = [];
        if (!empty($details['subjects'])) {
            $genres = array_slice($details['subjects'], 0, 8); // Tomar los primeros 8
        }

        // Obtener nombres de autores
        $authors = [];
        if (!empty($details['authors'])) {
            foreach ($details['authors'] as $authorItem) {
                if (!empty($authorItem['author']['key'])) {
                    $authorKey = str_replace('/authors/', '', $authorItem['author']['key']);
                    $authorData = Http::get("https://openlibrary.org/authors/{$authorKey}.json")->json();
                    if (!empty($authorData['name'])) {
                        $authors[] = $authorData['name'];
                    }
                }
            }
        }

        return [
            'external_id' => $cleanId,
            'title' => $details['title'],
            'cover_url' => isset($details['covers'][0]) ? "https://covers.openlibrary.org/b/id/{$details['covers'][0]}-L.jpg" : null,
            'synopsis' => $this->translateText($description),
            'type' => 'Libro',
            'source' => 'OpenLibrary',
            'genres' => $genres,
            'categories' => [],
            'year' => isset($details['first_publish_date']) ? substr($details['first_publish_date'], -4) : null,
            'trailer_url' => null,
            'images' => [],
            'episodes' => null,
            'chapters' => null,
            'studios' => [],
            'authors' => $authors,
            'media_type' => 'book'
        ];
    }

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
}