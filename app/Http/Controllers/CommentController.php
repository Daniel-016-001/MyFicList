<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Comment;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Guardar un nuevo comentario
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'media_id' => 'required|exists:media,id',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'media_id' => $request->media_id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Comentario publicado');
    }

    /**
     * Mostrar comentarios de un media
     */
    public function index($id)
    {
        $comments = Comment::where('media_id', $id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return response()->json($comments);
    }
}