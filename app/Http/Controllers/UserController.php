<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Ver el perfil público de un usuario
     */
    public function show(User $user)
    {
        $user->load(['mediaLists.items.media']);

        $totalCompleted = $user->userLists()->where('status', 'completed')->count();

        $mediaLists = $user->mediaLists()
            ->when(auth()->id() !== $user->id, fn($query) => $query->where('is_public', true))
            ->with(['items.media', 'likes'])
            ->get();

        $likesOnComments = $user->comments()->withCount('likes')->get()->sum('likes_count');
        $likesOnPosts = $user->forumPosts()->withCount('likes')->get()->sum('likes_count');
        $likesOnLists = $user->mediaLists()->withCount('likes')->get()->sum('likes_count');
        $totalLikes = $likesOnComments + $likesOnPosts + $likesOnLists;

        // Registro de actividad (logros)
        $recentComments = $user->comments()->with(['media', 'likes'])->latest()->take(5)->get();
        $recentListItems = $user->userLists()->with('media')->latest()->take(5)->get();
        $recentPosts = $user->forumPosts()->latest()->take(5)->get();

        $followers = $user->followers()->get();
        $following = $user->following()->get();

        return view('users.profile', [
            'user' => $user,
            'totalCompleted' => $totalCompleted,
            'totalLikes' => $totalLikes,
            'mediaLists' => $mediaLists,
            'recentComments' => $recentComments,
            'recentListItems' => $recentListItems,
            'recentPosts' => $recentPosts,
            'followers' => $followers,
            'following' => $following,
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