<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query();

        // Mostrar SOLO los títulos que han sido importados en su totalidad (con detalles completos).
        // Esto evita que el catálogo se llene de resultados de búsqueda parciales que rompen los filtros.
        if (!$request->filled('search')) {
            $query->where('extra_data->full_details_loaded', true);
        }

        // Filtro por tipo de media
        if ($request->filled('type')) {
            $query->where('media_type', $request->type);
        }

        // Filtro por plataforma (para videojuegos)
        if ($request->filled('platform')) {
            $platform = $request->platform;
            $query->where(function($q) use ($platform) {
                $q->whereJsonContains('extra_data->platforms', $platform)
                  ->orWhere('extra_data->platforms', 'like', '%' . $platform . '%');
            });
        }

        // Filtro por género (dentro de extra_data JSON)
        if ($request->filled('genre')) {
            $genre = $request->genre;
            $escapedGenre = trim(json_encode($genre), '"');
            $escapedGenreSafe = str_replace('\\', '_', $escapedGenre);

            $query->where(function($q) use ($genre, $escapedGenreSafe) {
                $q->whereJsonContains('extra_data->genres', $genre)
                  ->orWhere('extra_data', 'like', '%"' . $escapedGenreSafe . '"%');
            });
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

        // Obtener todas las plataformas únicas para el filtro
        $allPlatforms = Media::where('media_type', 'game')
            ->pluck('extra_data')
            ->filter()
            ->map(fn($data) => $data['platforms'] ?? [])
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        $mediaLists = auth()->check() ? auth()->user()->mediaLists : collect();

        return view('explore', compact('mediaItems', 'allGenres', 'allPlatforms', 'mediaLists'));
    }
}
