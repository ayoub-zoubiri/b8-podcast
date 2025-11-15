<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques (sans authentification)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (avec authentification)
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/resetPassword', [AuthController::class, 'resetPassword']);

    // Podcasts - Lecture (tous les utilisateurs)
    Route::get('/podcasts', [PodcastController::class, 'index']);
    Route::get('/podcasts/{id}', [PodcastController::class, 'show']);

    // Podcasts - Création/Modification (animateur et admin)
    Route::post('/podcasts', [PodcastController::class, 'store']);
    Route::put('/podcasts/{id}', [PodcastController::class, 'update']);
    Route::delete('/podcasts/{id}', [PodcastController::class, 'destroy']);

    // Épisodes - Lecture (tous les utilisateurs)
    Route::get('/podcasts/{podcast_id}/episodes', [EpisodeController::class, 'index']);
    Route::get('/episodes/{id}', [EpisodeController::class, 'show']);

    // Épisodes - Création/Modification (animateur et admin)
    Route::post('/podcasts/{podcast_id}/episodes', [EpisodeController::class, 'store']);
    Route::put('/episodes/{id}', [EpisodeController::class, 'update']);
    Route::delete('/episodes/{id}', [EpisodeController::class, 'destroy']);

    // Animateurs (tous les utilisateurs)
    Route::get('/hosts', [HostController::class, 'index']);
    Route::get('/hosts/{id}', [HostController::class, 'show']);

    // Recherche (tous les utilisateurs)
    Route::get('/search/podcasts', [SearchController::class, 'searchPodcasts']);
    Route::get('/search/episodes', [SearchController::class, 'searchEpisodes']);

    // Gestion des utilisateurs (admin uniquement)
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
