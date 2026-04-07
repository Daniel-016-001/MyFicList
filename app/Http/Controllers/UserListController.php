<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserList;

class UserListController extends Controller
{
    public function store(Request $request)
    {
        // Validamos los datos
        $request->validate([
            'media_id' => 'required|exists:media,id',
            'status' => 'required',
            'score' => 'nullable|integer|min:1|max:10'
        ]);

        // IMPORTANTE: Como aún no tenemos login, usaremos el ID 1 por defecto
        // Asegúrate de tener un usuario con ID 1 en tu tabla 'users' de MySQL
        UserList::updateOrCreate(
            ['user_id' => 1, 'media_id' => $request->media_id], // Si ya existe, lo actualiza
            [
                'status' => $request->status,
                'score' => $request->score,
                'progress' => 0 // Por ahora simplificado
            ]
        );

        return back()->with('success', '¡Lista actualizada!');
    }
}