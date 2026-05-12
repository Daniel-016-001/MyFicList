<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForumPostRequest;
use App\Models\ForumPost;
use App\Models\MediaList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ForumController extends Controller
{
    private const CATEGORIES = [
        'all' => 'Todas',
        'general' => 'General',
        'recomendaciones' => 'Recomendaciones',
        'discusion' => 'Discusión',
        'spoilers' => 'Spoilers',
    ];

    public function index(Request $request)
    {
        $selectedCategory = $request->query('category', 'all');

        $posts = ForumPost::with(['user', 'media'])
            ->when($selectedCategory !== 'all', fn($query) => $query->where('category', $selectedCategory))
            ->whereHas('user')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $publicLists = MediaList::with(['user', 'items.media'])
            ->where('is_public', true)
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('forum', [
            'posts' => $posts,
            'publicLists' => $publicLists,
            'categories' => self::CATEGORIES,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function store(StoreForumPostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['category'] = $data['category'] ?? 'general';

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('forum-attachments', 'public');
        }

        ForumPost::create($data);

        return redirect()->route('forum.index')->with('success', 'Tu publicación se ha creado correctamente.');
    }
}
