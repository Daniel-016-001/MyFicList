<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use App\Services\AnimeSearchService;
use Illuminate\Support\Facades\Http;

class MediaController extends Controller
{
    protected AnimeSearchService $animeSearchService;

    public function __construct(AnimeSearchService $animeSearchService)
    {
        $this->animeSearchService = $animeSearchService;
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type');

        if (!$query || !$type) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Buscar múltiples resultados
        $results = $this->animeSearchService->searchMultiple($query, $type);
        
        // Si no hay resultados
        if (empty($results)) {
            return response()->json(['error' => 'No results found'], 404);
        }

        // Si hay solo 1 resultado y está en BD local, ir directamente
        if (count($results) === 1 && $results[0]['is_stored'] === true) {
            return redirect()->route('media.show', $results[0]['id']);
        }

        // Mostrar página de resultados
        return view('media_results', compact('results', 'type', 'query'));
    }

    public function show($id)
    {
        $item = Media::findOrFail($id);
        return view('media_show', compact('item'));
    }

    /**
     * Agregar media desde búsqueda de resultados
     */
    public function addFromSearch(Request $request)
    {
        $type = $request->input('type');
        $source = $request->input('source');
        $externalId = $request->input('external_id');
        $title = $request->input('title');

        // Si ya existe en BD, sino integrarlo
        $existing = Media::where('external_id', $externalId)
            ->where('source', $source)
            ->first();

        if ($existing) {
            return redirect()->route('media.show', $existing->id);
        }

        // Buscar los datos completos según la fuente
        if ($source === 'Local') {
            return redirect()->route('media.show', $externalId);
        }

        $dataToSave = null;

        if (in_array($type, ['anime', 'manga'])) {
            // Obtener datos completos de Jikan o TMDB
            if ($source === 'Jikan') {
                $dataToSave = $this->getJikanDetails($externalId, $type);
            } elseif ($source === 'TMDB') {
                $dataToSave = $this->getTmdbDetails($externalId, $type);
            }
        } elseif (in_array($type, ['movie', 'series']) && $source === 'TMDB') {
            $dataToSave = $this->getTmdbDetails($externalId, $type);
        } elseif ($type === 'game' && $source === 'RAWG') {
            $dataToSave = $this->getRawgDetails($externalId);
        }

        if ($dataToSave) {
            $dataToSave['media_type'] = $type;
            $dataToSave['source'] = $source;
            $newMedia = Media::create($dataToSave);
            return redirect()->route('media.show', $newMedia->id);
        }

        return back()->with('error', 'No se pudo agregar el contenido');
    }

    private function fetchFromExternalApi($query, $type)
    {
        $dataToSave = null;

        switch ($type) {
            case 'movie':
            case 'series':
                $tmdbType = ($type == 'movie') ? 'movie' : 'tv';
                $response = Http::withToken(config('services.tmdb.token'))
                    ->get("https://api.themoviedb.org/3/search/{$tmdbType}", ['query' => $query]);

                $basic = $response->json()['results'][0] ?? null;

                if ($basic) {
                    $details = Http::withToken(config('services.tmdb.token'))
                        ->get("https://api.themoviedb.org/3/{$tmdbType}/{$basic['id']}", [
                            'append_to_response' => 'videos'
                        ])->json();

                    $trailer = collect($details['videos']['results'] ?? [])->where('type', 'Trailer')->first();

                    $dataToSave = [
                        'external_id' => $basic['id'],
                        'title' => $basic['title'] ?? $basic['name'],
                        'cover_url' => 'https://image.tmdb.org/t/p/w500' . $basic['poster_path'],
                        'synopsis' => $basic['overview'],
                        'source' => 'TMDB',
                        'extra_data' => [
                            'backdrop' => 'https://image.tmdb.org/t/p/original' . ($basic['backdrop_path'] ?? ''),
                            'trailer_url' => $trailer ? 'https://www.youtube.com/embed/' . $trailer['key'] : null,
                            'rating' => $basic['vote_average'] ?? 'N/A',
                            'release_date' => $basic['release_date'] ?? $basic['first_air_date'] ?? 'N/A'
                        ]
                    ];
                }
                break;

            case 'game':
                $search = Http::get("https://api.rawg.io/api/games", [
                    'key' => config('services.rawg.key'),
                    'search' => $query,
                    'page_size' => 1
                ])->json()['results'][0] ?? null;

                if ($search) {
                    $details = Http::get("https://api.rawg.io/api/games/{$search['id']}", [
                        'key' => config('services.rawg.key')
                    ])->json();

                    $dataToSave = [
                        'external_id' => $search['id'],
                        'title' => $search['name'],
                        'cover_url' => $search['background_image'],
                        'synopsis' => $details['description_raw'] ?? $details['description'] ?? 'No description available.',
                        'source' => 'RAWG',
                        'extra_data' => [
                            'backdrop' => $search['background_image'],
                            'screenshots' => collect($search['short_screenshots'] ?? [])->pluck('image')->toArray(),
                            'metacritic' => $search['metacritic'] ?? 'N/A',
                            'platforms' => collect($search['platforms'] ?? [])->pluck('platform.name')->toArray()
                        ]
                    ];
                }
                break;
        }

        return $dataToSave;
    }

    /**
     * Obtiene detalles completos de Jikan
     */
    private function getJikanDetails($malId, $type): ?array
    {
        try {
            $endpoint = ($type === 'manga') ? 'manga' : 'anime';
            $response = Http::get("https://api.jikan.moe/v4/{$endpoint}/{$malId}");
            $item = $response->json()['data'];

            $translator = new \Stichoza\GoogleTranslate\GoogleTranslate();
            $translator->setSource('auto');
            $translator->setTarget('es');

            return [
                'external_id' => $item['mal_id'],
                'title' => $translator->translate($item['title']),
                'source' => 'Jikan',
                'cover_url' => $item['images']['jpg']['large_image_url'],
                'synopsis' => $translator->translate($item['synopsis'] ?? ''),
                'extra_data' => [
                    'score' => $item['score'] ?? 'N/A',
                    'status' => $item['status'] ?? 'N/A',
                    'episodes' => $item['episodes'] ?? null,
                ]
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtiene detalles completos de TMDB
     */
    private function getTmdbDetails($tmdbId, $type): ?array
    {
        try {
            $tmdbType = ($type === 'movie') ? 'movie' : 'tv';
            $details = Http::withToken(config('services.tmdb.token'))
                ->get("https://api.themoviedb.org/3/{$tmdbType}/{$tmdbId}", [
                    'append_to_response' => 'videos',
                    'language' => 'es-ES'
                ])->json();

            $trailer = collect($details['videos']['results'] ?? [])
                ->where('type', 'Trailer')
                ->first();

            return [
                'external_id' => $tmdbId,
                'title' => $details['title'] ?? $details['name'],
                'source' => 'TMDB',
                'cover_url' => 'https://image.tmdb.org/t/p/w500' . ($details['poster_path'] ?? ''),
                'synopsis' => $details['overview'] ?? '',
                'extra_data' => [
                    'backdrop' => 'https://image.tmdb.org/t/p/original' . ($details['backdrop_path'] ?? ''),
                    'trailer_url' => $trailer ? 'https://www.youtube.com/embed/' . $trailer['key'] : null,
                    'rating' => $details['vote_average'] ?? 'N/A',
                    'release_date' => $details['release_date'] ?? $details['first_air_date'] ?? 'N/A',
                ]
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtiene detalles completos de RAWG
     */
    private function getRawgDetails($rawgId): ?array
    {
        try {
            $details = Http::get("https://api.rawg.io/api/games/{$rawgId}", [
                'key' => config('services.rawg.key')
            ])->json();

            return [
                'external_id' => $rawgId,
                'title' => $details['name'],
                'source' => 'RAWG',
                'cover_url' => $details['background_image'],
                'synopsis' => $details['description_raw'] ?? $details['description'] ?? 'Sin descripción',
                'extra_data' => [
                    'backdrop' => $details['background_image'],
                    'metacritic' => $details['metacritic'] ?? 'N/A',
                ]
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}