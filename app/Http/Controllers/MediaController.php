<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Http;

class MediaController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type');

        if (!$query || !$type) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $localMedia = Media::where('title', 'LIKE', "%{$query}%")
            ->where('media_type', $type)
            ->first();

        if ($localMedia) {
            return redirect()->route('media.show', $localMedia->id);
        }

        $dataToSave = $this->fetchFromExternalApi($query, $type);

        if ($dataToSave) {
            $dataToSave['media_type'] = $type;
            // Aquí Media::create usará el 'cast' del modelo correctamente
            $newMedia = Media::create($dataToSave);
            return redirect()->route('media.show', $newMedia->id);
        }

        return response()->json(['error' => 'No results found'], 404);
    }

    public function show($id)
    {
        $item = Media::findOrFail($id);
        return view('media_show', compact('item'));
    }

    private function fetchFromExternalApi($query, $type)
    {
        $dataToSave = null;

        switch ($type) {
            case 'anime':
            case 'manga':
                $endpoint = ($type == 'anime') ? 'anime' : 'manga';
                $response = Http::get("https://api.jikan.moe/v4/{$endpoint}", ['q' => $query, 'limit' => 1]);
                $item = $response->json()['data'][0] ?? null;

                if ($item) {
                    $dataToSave = [
                        'external_id' => $item['mal_id'],
                        'title' => $item['title'],
                        'cover_url' => $item['images']['jpg']['large_image_url'],
                        'synopsis' => $item['synopsis'],
                        'extra_data' => [ // <--- ARRAY SIMPLE, SIN JSON_ENCODE
                            'trailer_url' => ($type == 'anime') ? ($item['trailer']['embed_url'] ?? null) : null,
                            'score' => $item['score'] ?? 'N/A',
                            'status' => $item['status'],
                            'chapters' => $item['chapters'] ?? null,
                            'volumes' => $item['volumes'] ?? null,
                            'backdrop' => null
                        ]
                    ];
                }
                break;

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
                        'extra_data' => [ // <--- ARRAY SIMPLE
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
                        'extra_data' => [ // <--- ARRAY SIMPLE
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
}