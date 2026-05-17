<?php

namespace App\Http\Controllers;

use App\Services\MediaIntegrationService;
use App\Services\SearchService; // Importamos el servicio de búsqueda
use Illuminate\Http\Request;
use App\Models\Media;

class MediaController extends Controller
{
    protected $mediaService;
    protected $searchService;

    public function __construct(MediaIntegrationService $mediaService, SearchService $searchService)
    {
        $this->mediaService = $mediaService;
        $this->searchService = $searchService;
    }

    /**
     * Muestra la ficha técnica de un media ya guardado en la BD
     */
    public function show($id)
    {
        $media = Media::findOrFail($id);

        // Lazy load de detalles completos si no se han cargado antes o si falta el trailer
        $extra = $media->extra_data ?? [];
        if (!array_key_exists('full_details_loaded', $extra) || $extra['full_details_loaded'] !== true || !array_key_exists('trailer_url', $extra)) {
            $updatedMedia = $this->mediaService->importToDatabase($media->external_id, $media->source, $media->media_type);
            if ($updatedMedia) {
                $media = $updatedMedia;
            }
        }

        return view('media_show', compact('media'));
    }

    /**
     * Método para la búsqueda simple (por tipo)
     */
    public function search(Request $request)
    {
        $query = $request->input('query', $request->input('q', ''));
        $type = $request->input('type', 'anime');

        $results = $this->searchService->searchMultiple($query, $type);

        // Filtrar resultados que no tienen imagen de portada
        $results = array_filter($results, function ($result) {
            return !empty($result['cover_url']);
        });

        if (!empty($results)) {
            foreach ($results as &$result) {
                try {
                    $importedMedia = $this->mediaService->importSearchResult($result);
                    if ($importedMedia) {
                        $result['id'] = $importedMedia->id;
                        $result['is_stored'] = true;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $mediaLists = auth()->check() ? auth()->user()->mediaLists : collect();

        return view('search', [
            'results' => $results,
            'query' => $query,
            'type' => $type,
            'mediaLists' => $mediaLists
        ]);
    }

    public function searchUnified(Request $request)
    {
        // 1. Validamos que el campo 'query' esté presente y sea un texto
        $query = $request->input('query');

        // Si el usuario no escribió nada, lo mandamos de vuelta con un mensaje
        if (empty($query)) {
            return redirect()->back()->with('error', 'Por favor, introduce un término de búsqueda.');
        }

        // Si se seleccionó un tipo específico desde la portada, redirigimos a la búsqueda específica
        $type = $request->input('type', 'all');
        if ($type !== 'all') {
            return redirect()->route('media.search', [
                'query' => $query, 
                'type' => $type, 
                'safe' => $request->input('safe')
            ]);
        }

        // Ahora estamos seguros de que $query es un string
        $results = $this->mediaService->getUnifiedResults((string) $query);

        // Filtrar resultados que no tienen imagen de portada
        $results = array_filter($results, function ($result) {
            return !empty($result['cover_url']);
        });

        if (!empty($results)) {
            foreach ($results as &$result) {
                try {
                    $importedMedia = $this->mediaService->importSearchResult($result);
                    if ($importedMedia) {
                        $result['id'] = $importedMedia->id;
                        $result['is_stored'] = true;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }


        $mediaLists = auth()->check() ? auth()->user()->mediaLists : collect();

        return view('search', [
            'results' => $results,
            'query' => $query,
            'is_unified' => true,
            'mediaLists' => $mediaLists
        ]);
    }

    public function suggestions(Request $request)
    {
        $query = trim((string) $request->query('query', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $suggestions = Media::where('title', 'like', "%{$query}%")
            ->orderBy('title')
            ->limit(10)
            ->get(['id', 'title']);

        return response()->json($suggestions);
    }

    public function addFromSearch(Request $request)
    {
        $media = $this->mediaService->importToDatabase(
            $request->input('external_id'),
            $request->input('source'),
            $request->input('media_type') // Asegúrate que en el form se llame media_type o cámbialo aquí
        );

        if (!$media || !$media->id) {
            return back()->with('error', 'Error al importar o contenido no disponible.');
        }

        return redirect()->route('media.show', $media->id);
    }

    /**
     * Muestra detalles de un contenido desde APIs externas (sin importar)
     */
    public function details($externalId, $source, $type)
    {
        // Importamos (o actualizamos) con detalles completos
        $media = $this->mediaService->importToDatabase($externalId, $source, $type);

        if (!$media || !$media->id) {
            return redirect()->back()->with('error', 'No se pudieron obtener los detalles del contenido.');
        }

        // Redirigimos a la vista permanente que ya tiene toda la lógica de listas y comentarios
        return redirect()->route('media.show', $media->id);
    }
}