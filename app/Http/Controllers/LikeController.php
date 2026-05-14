<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string',
        ]);

        $userId = Auth::id();
        $likeableId = $request->likeable_id;
        $likeableType = $request->likeable_type;

        // Resolve the model class (ensure it's one of the allowed types)
        $allowedTypes = [
            'comment' => \App\Models\Comment::class,
            'post' => \App\Models\ForumPost::class,
            'list' => \App\Models\MediaList::class,
            'media_list' => \App\Models\MediaList::class,
        ];

        if (!array_key_exists($likeableType, $allowedTypes)) {
            return response()->json(['error' => 'Invalid likeable type'], 400);
        }

        $modelClass = $allowedTypes[$likeableType];

        $like = Like::where([
            'user_id' => $userId,
            'likeable_id' => $likeableId,
            'likeable_type' => $modelClass,
        ])->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_id' => $likeableId,
                'likeable_type' => $modelClass,
            ]);
            $status = 'liked';
        }

        $count = $modelClass::find($likeableId)->likes()->count();

        return response()->json([
            'status' => $status,
            'count' => $count,
        ]);
    }
}
