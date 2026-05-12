<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class PopularMediaController extends Controller
{
    public function index()
    {
        // 1. Traemos los medios con su nota media y número de votos directamente de la DB
        $popularByCategory = Media::query()
            ->withAvg('userLists as avg_score', 'score') // Calcula la media de la columna 'score'
            ->withCount(['userLists as ratings_count' => function ($query) {
                $query->whereNotNull('score'); // Solo cuenta si tienen nota
            }])
            ->get()
            // 2. Filtramos solo los que tienen votos para no mostrar items vacíos
            ->filter(fn($media) => $media->ratings_count > 0)
            // 3. Agrupamos por categoría (Anime, Película, Juego...)
            ->groupBy('media_type')
            // 4. Ordenamos cada grupo por nota y tomamos los 5 mejores
            ->map(function ($items) {
                return $items->sortByDesc('avg_score')->take(5);
            });

        $mediaLists = auth()->check() ? auth()->user()->mediaLists : collect();

        return view('dashboard', compact('popularByCategory', 'mediaLists'));
    }
}