<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserListController;
use Illuminate\Support\Facades\Route;

// 1. PUBLICAS: Cualquiera puede entrar
Route::get('/', function () {
    return view('home'); // Tu buscador
});

// El buscador y la ficha técnica también son públicos (para que la gente vea info)
Route::get('/search', [MediaController::class, 'search'])->name('media.search');
Route::get('/search/unified', [MediaController::class, 'searchUnified'])->name('media.search.unified');
Route::post('/media/add-from-search', [MediaController::class, 'addFromSearch'])->name('media.add-from-search');

// Página de detalles y comentarios
Route::get('/catalogo/{id}', [MediaController::class, 'show'])->name('media.show');
Route::get('/media/{id}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('media.comments');
Route::post('/media/{id}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->middleware(['auth', 'verified'])->name('media.comments.store');

// 2. PRIVADAS: Solo para usuarios registrados
Route::middleware(['auth', 'verified'])->group(function () {
    
    // El Dashboard - Contenidos mejor valorados por categoría
    Route::get('/dashboard', function () {
        $categories = ['anime', 'manga', 'movie', 'series', 'game', 'book'];
        $popularByCategory = [];
        
        foreach ($categories as $cat) {
            $popularByCategory[$cat] = \App\Models\Media::where('media_type', $cat)
                ->with(['userRatings' => function($q) {
                    $q->whereNotNull('score');
                }])
                ->withCount('userRatings')
                ->get()
                ->map(function ($media) {
                    $media->average_score = $media->userRatings->avg('score');
                    return $media;
                })
                ->filter(fn($m) => $m->average_score !== null)
                ->sortByDesc('average_score')
                ->take(5);
        }
        
        return view('dashboard', compact('popularByCategory'));
    })->name('dashboard');

    // Guardar en la lista (ahora protegida por middleware)
    Route::post('/user-list', [UserListController::class, 'store'])->name('user-list.store');
    Route::put('/user-list/{id}', [UserListController::class, 'update'])->name('user-list.update');
    Route::delete('/user-list/{id}', [UserListController::class, 'destroy'])->name('user-list.destroy');

    // Perfil de usuario (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';