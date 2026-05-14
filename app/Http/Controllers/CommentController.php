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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id'   => 'required|integer',
            'content'          => 'required|string|max:1000',
            'parent_id'        => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id'          => auth()->id(),
            'commentable_type' => $validated['commentable_type'],
            'commentable_id'   => $validated['commentable_id'],
            'content'          => $validated['content'],
            'parent_id'        => $validated['parent_id'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Comentario publicado con éxito',
                'comment' => $comment->load('user')
            ]);
        }

        return back()->with('success', '¡Comentario publicado con éxito!');
    }

    /**
     * Eliminar un comentario
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comentario eliminado.');
    }
}