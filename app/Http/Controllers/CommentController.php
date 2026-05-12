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
        // Aseguramos que el media_id esté presente en el request incluso si no viene en el form
        if (!$request->has('media_id')) {
            $request->merge(['media_id' => $id]);
        }

        $validated = $request->validate([
            'media_id' => 'required|exists:media,id',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'media_id' => $validated['media_id'],
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Comentario publicado con éxito',
                'comment' => $comment->load('user')
            ]);
        }

        return redirect()->route('media.show', $id)->with('success', '¡Comentario publicado con éxito!');
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