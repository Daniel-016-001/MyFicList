<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\MediaList;
use App\Services\S3ImageService;
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
        'listas' => 'Listas',
    ];

    public function index(Request $request)
    {
        $selectedCategory = $request->query('category', 'all');
        $search = $request->query('search');

        if ($selectedCategory === 'listas') {
            $items = MediaList::with(['user', 'items.media', 'likes'])
                ->where('is_public', true)
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('username', 'like', "%{$search}%");
                        });
                })
                ->latest('updated_at')
                ->paginate(12)
                ->withQueryString();
        } elseif ($selectedCategory === 'all') {
            // Unir Publicaciones y Listas para "Todas"
            $posts = ForumPost::with(['user', 'media', 'likes'])
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('body', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($u) use ($search) {
                                $u->where('username', 'like', "%{$search}%");
                            });
                    });
                })
                ->whereHas('user')
                ->latest()
                ->get();

            $lists = MediaList::with(['user', 'items.media', 'likes'])
                ->where('is_public', true)
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('username', 'like', "%{$search}%");
                        });
                })
                ->latest('updated_at')
                ->get();

            // Combinar y paginar manualmente
            $merged = $posts->concat($lists)->sortByDesc(function ($item) {
                return $item->created_at ?? $item->updated_at;
            });

            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $perPage = 12;
            $currentItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->all();

            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $merged->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
            $items->appends($request->all());
        } else {
            $items = ForumPost::with(['user', 'media', 'likes'])
                ->where('category', $selectedCategory)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('body', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($u) use ($search) {
                                $u->where('username', 'like', "%{$search}%");
                            });
                    });
                })
                ->whereHas('user')
                ->latest()
                ->paginate(12)
                ->withQueryString();
        }

        $publicLists = MediaList::with(['user', 'items.media', 'likes'])
            ->where('is_public', true)
            ->withCount(['likes', 'comments'])
            ->orderByRaw('(likes_count + comments_count) DESC')
            ->orderBy('updated_at', 'DESC')
            ->take(5)
            ->get();

        return view('forum', [
            'items' => $items,
            'publicLists' => $publicLists,
            'categories' => self::CATEGORIES,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
        ]);
    }

    public function store(Request $request, S3ImageService $s3Service)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'nullable|string|in:general,recomendaciones,discusion,spoilers',
            'media_id' => 'nullable|exists:media,id',
            'attachment' => 'nullable|image|max:10240',
        ]);

        $data['user_id'] = auth()->id();
        $data['category'] = $data['category'] ?? 'general';

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $data['attachment_path'] = $s3Service->uploadForo($file);
        }

        ForumPost::create($data);

        return redirect()->route('forum.index')->with('success', 'Tu publicación se ha creado correctamente.');
    }

    public function destroy(ForumPost $post, S3ImageService $s3Service)
    {
        // Solo el dueño o un admin pueden borrar
        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            return back()->with('error', 'No tienes permiso para eliminar esta publicación.');
        }

        // Eliminar adjunto de S3 o disco local si existe
        if ($post->attachment_path) {
            if (str_starts_with($post->attachment_path, 'http')) {
                $s3Service->deleteFile($post->attachment_path);
            } else {
                Storage::disk('public')->delete($post->attachment_path);
            }
        }

        $post->delete();

        return redirect()->route('forum.index')->with('success', 'Publicación eliminada con éxito.');
    }
}
