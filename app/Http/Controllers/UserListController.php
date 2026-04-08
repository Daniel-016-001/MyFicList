<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserList;
use Illuminate\Support\Facades\Auth;

class UserListController extends Controller
{
    /**
     * Guardar un elemento en la lista del usuario
     */
    public function store(Request $request)
    {
        // Validar datos
        $request->validate([
            'media_id' => 'required|exists:media,id',
            'status' => 'required|in:watching,completed,on_hold,dropped,plan_to_watch',
            'score' => 'nullable|integer|min:1|max:10'
        ]);

        // Obtener el usuario autenticado
        $userId = Auth::id();

        // Crear o actualizar entrada en la lista
        UserList::updateOrCreate(
            ['user_id' => $userId, 'media_id' => $request->media_id],
            [
                'status' => $request->status,
                'score' => $request->score ?? null,
                'progress' => $request->progress ?? 0
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
            'score' => 'sometimes|integer|min:1|max:10',
            'progress' => 'sometimes|integer|min:0'
        ]);

        $userList->update($request->only(['status', 'score', 'progress']));

        return back()->with('success', '¡Lista actualizada!');
    }
}