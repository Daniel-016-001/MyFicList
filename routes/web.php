<?php

use App\Http\Controllers\PopularMediaController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\UserController; // Importante
use App\Http\Controllers\CommentController; // Importante
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MediaListController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;

// --- 1. RUTAS PÚBLICAS ---
Route::get('/', function () {
    return view('home'); 
})->name('home');

// Búsqueda y Catálogo
Route::get('/search', [MediaController::class, 'search'])->name('media.search');
Route::get('/search/unified', [MediaController::class, 'searchUnified'])->name('media.search.unified');
Route::get('/media/suggestions', [MediaController::class, 'suggestions'])->name('media.suggestions');
Route::get('/top', [PopularMediaController::class, 'index'])->name('dashboard');
Route::get('/explorar', [ExploreController::class, 'index'])->name('media.explore');
Route::get('/catalogo/{id}', [MediaController::class, 'show'])->name('media.show');
Route::get('/details/{external_id}/{source}/{type}', [MediaController::class, 'details'])->name('media.details');
Route::post('/media/import', [MediaController::class, 'addFromSearch'])->name('media.add-from-search');

// Comunidad (Pública para que se puedan ver perfiles de otros)
Route::get('/comunidad', [UserController::class, 'index'])->name('users.index');
Route::get('/u/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/foro', [ForumController::class, 'index'])->name('forum.index');
Route::get('/listas/{mediaList}', [MediaListController::class, 'show'])->name('media-lists.show');

// Comentarios (Ver es público, pero gestionado en las vistas de cada recurso)

// --- 2. RUTAS PRIVADAS (Requieren estar logueado) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // LISTA DEL USUARIO
    Route::get('/mi-lista', [UserListController::class, 'index'])->name('user-list.index');

    // IMPORTACIÓN: Movido a rutas públicas para que usuarios no registrados también puedan refrescar datos

    // MI LISTA: Gestión de lo que estoy viendo/he visto
    Route::post('/user-list', [UserListController::class, 'store'])->name('user-list.store');
    Route::put('/user-list/{id}', [UserListController::class, 'update'])->name('user-list.update');
    Route::delete('/user-list/{id}', [UserListController::class, 'destroy'])->name('user-list.destroy');

    // COMENTARIOS: Escribir y responder
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // LISTAS DE USUARIO
    Route::post('/media-lists', [MediaListController::class, 'store'])->name('media-lists.store');
    Route::put('/media-lists/{mediaList}', [MediaListController::class, 'update'])->name('media-lists.update');
    Route::delete('/media-lists/{mediaList}', [MediaListController::class, 'destroy'])->name('media-lists.destroy');

    // FORO: Crear y eliminar publicaciones
    Route::post('/foro', [ForumController::class, 'store'])->name('forum.store');
    Route::delete('/foro/{post}', [ForumController::class, 'destroy'])->name('forum.destroy');

    // PERFIL (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // LIKES
    Route::post('/like', [LikeController::class, 'toggle'])->name('like.toggle');

    // SEGUIR USUARIOS
    Route::post('/u/{user}/follow', [UserController::class, 'follow'])->name('users.follow');
});

require __DIR__.'/auth.php';