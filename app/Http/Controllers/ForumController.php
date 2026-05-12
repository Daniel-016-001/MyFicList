<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\MediaList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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

    public function store(Request $request)
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
            $fileName = 'foro/' . time() . '_' . uniqid() . '.jpg';

            // Procesar con Intervention Image v3
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scale(width: 1200);
            $encoded = $image->toJpeg(75)->toString();

            // Subir a S3
            Storage::disk('s3')->put($fileName, $encoded, 'public');
            $data['attachment_path'] = $fileName;
        }

        ForumPost::create($data);

        return redirect()->route('forum.index')->with('success', 'Tu publicación se ha creado correctamente.');
    }

    public function destroy(ForumPost $post)
    {
        // Solo el dueño o un admin pueden borrar
        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            return back()->with('error', 'No tienes permiso para eliminar esta publicación.');
        }

        // Eliminar adjunto de S3 si existe
        if ($post->attachment_path) {
            Storage::disk('s3')->delete($post->attachment_path);
        }

        $post->delete();

        return redirect()->route('forum.index')->with('success', 'Publicación eliminada con éxito.');
    }
}
