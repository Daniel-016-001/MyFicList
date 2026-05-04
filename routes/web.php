<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\UserController; // Importante
use App\Http\Controllers\CommentController; // Importante
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MediaListController;
use Illuminate\Support\Facades\Route;

// --- 1. RUTAS PÚBLICAS ---
Route::get('/', function () {
    return view('home'); 
})->name('home');

// Búsqueda y Catálogo
Route::get('/search', [MediaController::class, 'search'])->name('media.search');
Route::get('/search/unified', [MediaController::class, 'searchUnified'])->name('media.search.unified');
Route::get('/media/suggestions', [MediaController::class, 'suggestions'])->name('media.suggestions');
Route::get('/catalogo/{id}', [MediaController::class, 'show'])->name('media.show');
Route::get('/details/{external_id}/{source}/{type}', [MediaController::class, 'details'])->name('media.details');

// Comunidad (Pública para que se puedan ver perfiles de otros)
Route::get('/comunidad', [UserController::class, 'index'])->name('users.index');
Route::get('/u/{username}', [UserController::class, 'show'])->name('users.show');
Route::get('/foro', [ForumController::class, 'index'])->name('forum.index');
Route::get('/listas/{mediaList}', [MediaListController::class, 'show'])->name('media-lists.show');

// Comentarios (Ver es público, escribir es privado)
Route::get('/media/{id}/comments', [CommentController::class, 'index'])->name('media.comments');


// --- 2. RUTAS PRIVADAS (Requieren estar logueado) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // DASHBOARD: La lógica que tienes en el closure está bien, 
    // pero si crece, muévela a un DashboardController.
    Route::get('/dashboard', function () {
        // Calcular popularidad basada en media de puntuaciones propias
        $avgScores = DB::table('user_lists')
            ->select('media_id', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as ratings_count'))
            ->whereNotNull('score')
            ->groupBy('media_id')
            ->get()
            ->keyBy('media_id');

        $popularByCategory = \App\Models\Media::all()
            ->filter(function($media) use ($avgScores) {
                return isset($avgScores[$media->id]);
            })
            ->groupBy('media_type')
            ->map(function($medias) use ($avgScores) {
                return $medias->map(function($media) use ($avgScores) {
                    $media->avg_score = $avgScores[$media->id]->avg_score;
                    $media->ratings_count = $avgScores[$media->id]->ratings_count;
                    return $media;
                })->sortByDesc('avg_score')->take(5);
            });

        return view('dashboard', compact('popularByCategory'));
    })->name('dashboard');

    // LISTA DEL USUARIO
    Route::get('/mi-lista', [UserListController::class, 'index'])->name('user-list.index');

    // IMPORTACIÓN: Solo usuarios registrados pueden meter contenido nuevo a la BD
    Route::post('/media/import', [MediaController::class, 'addFromSearch'])->name('media.add-from-search');

    // MI LISTA: Gestión de lo que estoy viendo/he visto
    Route::post('/user-list', [UserListController::class, 'store'])->name('user-list.store');
    Route::put('/user-list/{id}', [UserListController::class, 'update'])->name('user-list.update');
    Route::delete('/user-list/{id}', [UserListController::class, 'destroy'])->name('user-list.destroy');

    // COMENTARIOS: Escribir y responder
    Route::post('/media/{id}/comments', [CommentController::class, 'store'])->name('media.comments.store');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // LISTAS DE USUARIO
    Route::post('/media-lists', [MediaListController::class, 'store'])->name('media-lists.store');
    Route::put('/media-lists/{mediaList}', [MediaListController::class, 'update'])->name('media-lists.update');
    Route::delete('/media-lists/{mediaList}', [MediaListController::class, 'destroy'])->name('media-lists.destroy');

    // FORO: Crear nueva publicación
    Route::post('/foro', [ForumController::class, 'store'])->name('forum.store');

    // PERFIL (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';