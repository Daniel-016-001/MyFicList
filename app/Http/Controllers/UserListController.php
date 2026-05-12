<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserList;
use App\Models\Media;
use App\Models\MediaList;
use App\Services\MediaIntegrationService;
use Illuminate\Support\Facades\Auth;

class UserListController extends Controller
{
    protected $mediaService;

    public function __construct(MediaIntegrationService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Guardar un elemento en la lista del usuario
     */
    public function index()
    {
        $mediaLists = MediaList::with(['items.media'])
            ->where('user_id', Auth::id())
            ->get()
            ->sortBy(fn($list) => $list->name);

        return view('user_list.index', compact('mediaLists'));
    }

    public function store(Request $request)
    {
        // Validar datos
        $request->validate([
            'status' => 'required|in:watching,completed,on_hold,dropped,plan_to_watch',
            'score' => 'nullable|integer|min:0|max:10',
            'progress' => 'nullable|integer|min:0',
            // media_list_id can be a numeric ID or the string 'new'
            'media_list_id' => 'nullable',
            'new_list_name' => 'required_if:media_list_id,new|nullable|string|max:100',
        ]);

        $userId = Auth::id();
        $mediaId = null;
        $mediaListId = null;
        $mediaType = null;

        // Si se proporciona external_id, importar el media si no existe
        if ($request->has('external_id') && $request->has('source') && $request->has('media_type')) {
            $media = Media::where('external_id', $request->external_id)
                         ->where('source', $request->source)
                         ->first();

            if (!$media) {
                // Importar el media
                $media = $this->mediaService->importToDatabase(
                    $request->external_id,
                    $request->source,
                    $request->media_type
                );

                if (!$media) {
                    return back()->with('error', 'Error al importar el contenido. Inténtalo de nuevo.');
                }
            }

            $mediaId = $media->id;
            $mediaType = $media->media_type;
        } elseif ($request->has('media_id')) {
            // Validar que el media existe
            $request->validate([
                'media_id' => 'required|exists:media,id'
            ]);
            $media = Media::find($request->media_id);
            $mediaId = $media->id;
            $mediaType = $media->media_type;
        } else {
            return back()->with('error', 'Datos insuficientes para agregar a la lista.');
        }

        // Validar que el progreso no exceda el contenido total
        $total = match ($media->media_type) {
            'anime', 'series' => data_get($media->extra_data, 'episodes', 0),
            'manga', 'book' => data_get($media->extra_data, 'chapters', 0),
            default => null,
        };
        if ($media->media_type !== 'game' && $total !== null && $request->progress > $total) {
            return back()->withErrors(['progress' => 'El progreso no puede ser mayor al contenido total.']);
        }

        if ($request->filled('media_list_id') && $request->media_list_id !== 'new') {
            $mediaList = MediaList::where('id', $request->media_list_id)
                ->where('user_id', $userId)
                ->first();

            if ($mediaList) {
                $mediaListId = $mediaList->id;
            }
        } elseif ($request->filled('new_list_name')) {
            $newList = MediaList::create([
                'user_id' => $userId,
                'name' => $request->new_list_name,
                'is_public' => $request->has('is_public')
            ]);
            $mediaListId = $newList->id;
        }

        if (!$mediaListId) {
            $defaultList = MediaList::firstOrCreate(
                ['user_id' => $userId, 'name' => 'Mi Lista'],
                ['is_public' => false]
            );
            $mediaListId = $defaultList->id;
        }

        // Crear o actualizar entrada en la lista (Buscamos solo por usuario y medio para permitir "mover" de lista)
        UserList::updateOrCreate(
            ['user_id' => $userId, 'media_id' => $mediaId],
            [
                'media_list_id' => $mediaListId,
                'status' => $request->status,
                'score' => $request->filled('score') ? intval($request->score) : null,
                'progress' => ($mediaType === 'game') ? 0 : ($request->progress ?? 0)
            ]
        );

        return back()->with('success', '¡Elemento agregado a tu lista!');
    }

    /**
     * Eliminar un elemento de la lista del usuario
     */
    public function destroy($id)
    {
        $userList = UserList::findOrFail($id);

        // Verificar que pertenece al usuario autenticado
        if ($userList->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $userList->delete();

        return back()->with('success', '¡Elemento eliminado de tu lista!');
    }

    /**
     * Actualizar solo el estado o puntuación
     */
    public function update(Request $request, $id)
    {
        $userList = UserList::findOrFail($id);

        if ($userList->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'status' => 'sometimes|in:watching,completed,on_hold,dropped,plan_to_watch',
            'score' => 'sometimes|integer|min:0|max:10',
            'progress' => 'sometimes|integer|min:0'
        ]);

        // Validar que el progreso no exceda el contenido total
        if ($request->has('progress')) {
            $total = match ($userList->media->media_type) {
                'anime', 'series' => data_get($userList->media->extra_data, 'episodes', 0),
                'manga', 'book' => data_get($userList->media->extra_data, 'chapters', 0),
                default => null,
            };
            if ($total !== null && $request->progress > $total) {
                return back()->withErrors(['progress' => 'El progreso no puede ser mayor al contenido total.']);
            }
        }

        $userList->update($request->only(['status', 'score', 'progress']));

        return back()->with('success', '¡Lista actualizada!');
    }
}