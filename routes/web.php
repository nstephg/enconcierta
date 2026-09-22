<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpotifyController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->perfil_completado
            ? redirect()->route('dashboard')
            : redirect()->route('profile.setup');
    }
    return app(HomeController::class)();
})->name('home');

Route::middleware(['auth'])->group(function () {
    // Configuración inicial de perfil (Onboarding)
    Route::get('/perfil/configurar', [ProfileController::class, 'showSetup'])->name('profile.setup');
    Route::post('/perfil/configurar', [ProfileController::class, 'saveSetup'])->name('profile.setup.save');

    // Perfil de usuario (Propio si no hay ID, o de tercero si se pasa {id})
    Route::get('/perfil/{id?}', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    // Dashboard y Publicaciones
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/posts', [DashboardController::class, 'storePost'])->name('posts.store');
    Route::get('/posts/{id}', [DashboardController::class, 'showPost'])->name('posts.show');
    Route::post('/posts/{id}/like', [DashboardController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{id}/vote', [DashboardController::class, 'votePoll'])->name('posts.vote');
    Route::post('/posts/{id}/comments', [DashboardController::class, 'storeComment'])->name('posts.comments.store');
    Route::post('/comments/{id}/like', [DashboardController::class, 'toggleCommentLike'])->name('comments.like');

    // Spotify
    Route::get('/spotify/login', [SpotifyController::class, 'redirect'])->name('spotify.login');
    Route::get('/spotify/callback', [SpotifyController::class, 'callback'])->name('spotify.callback');
    Route::post('/spotify/disconnect', [SpotifyController::class, 'disconnect'])->name('spotify.disconnect');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');