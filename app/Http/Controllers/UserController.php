<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Ver el perfil público de un usuario
     */
    public function show(User $user) // Laravel ya lo busca por 'username' si lo configuraste en el modelo
    {
        $user->load(['mediaLists.items.media']);

        $totalCompleted = $user->userLists()->where('status', 'completed')->count();

        $mediaLists = $user->mediaLists()
            ->when(auth()->id() !== $user->id, fn($query) => $query->where('is_public', true))
            ->with('items.media')
            ->get();

        return view('users.profile', [
            'user' => $user,
            'totalCompleted' => $totalCompleted,
            'mediaLists' => $mediaLists,
        ]);
    }

    /**
     * Listado de usuarios (Comunidad)
     */
    public function index(Request $request)
    {
        $query = $request->input('search');

        $users = User::when($query, function ($q) use ($query) {
            return $q->where('username', 'like', "%{$query}%");
        })
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    /**
     * Sistema de seguir (opcional)
     */
    public function follow(User $user)
    {
        // Asumiendo que tienes una relación de muchos a muchos "followers"
        auth()->user()->following()->toggle($user->id);

        return back()->with('success', 'Operación realizada');
    }
}