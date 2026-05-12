<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query();

        // Filtro por tipo de media
        if ($request->filled('type')) {
            $query->where('media_type', $request->type);
        }

        // Filtro por género (dentro de extra_data JSON)
        if ($request->filled('genre')) {
            $query->whereJsonContains('extra_data->genres', $request->genre);
        }

        // Filtro por búsqueda de título
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Ordenar por más recientes por defecto, y por ID para evitar duplicados en paginación
        $mediaItems = $query
            ->withCount('userLists')
            ->withAvg('userRatings as avg_score', 'score')
            ->latest()
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Obtener todos los géneros únicos para el filtro
        $allGenres = Media::pluck('extra_data')
            ->filter()
            ->map(fn($data) => $data['genres'] ?? [])
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        $mediaLists = auth()->check() ? auth()->user()->mediaLists : collect();

        return view('explore', compact('mediaItems', 'allGenres', 'mediaLists'));
    }
}
