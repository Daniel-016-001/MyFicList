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
Route::post('/media/add-from-search', [MediaController::class, 'addFromSearch'])->name('media.add-from-search');
Route::get('/catalogo/{id}', [MediaController::class, 'show'])->name('media.show');

// 2. PRIVADAS: Solo para usuarios registrados
Route::middleware(['auth', 'verified'])->group(function () {
    
    // El Dashboard (será tu biblioteca)
    Route::get('/dashboard', function () {
        return view('dashboard');
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