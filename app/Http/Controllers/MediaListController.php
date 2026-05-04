<?php

namespace App\Http\Controllers;

use App\Models\MediaList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaListController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        Auth::user()->mediaLists()->create([
            'name' => $request->name,
            'is_public' => $request->boolean('is_public'),
        ]);

        return back()->with('success', 'Lista creada correctamente.');
    }

    public function update(Request $request, MediaList $mediaList)
    {
        if ($mediaList->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $mediaList->update([
            'name' => $request->name,
            'is_public' => $request->boolean('is_public'),
        ]);

        return back()->with('success', 'Lista actualizada.');
    }

    public function destroy(MediaList $mediaList)
    {
        if ($mediaList->user_id !== Auth::id()) {
            abort(403);
        }

        $mediaList->delete();

        return back()->with('success', 'Lista eliminada.');
    }

    public function show(MediaList $mediaList)
    {
        if (!$mediaList->is_public && Auth::id() !== $mediaList->user_id) {
            abort(403);
        }

        $mediaList->load('user', 'items.media');

        return view('media_lists.show', compact('mediaList'));
    }
}
