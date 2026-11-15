<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// Semua route di sini otomatis diawali /api

// GET /api/posts → semua post (hasil Sesi 5)
Route::get('/posts', [PostController::class, 'index']);

// GET /api/posts/{id} → satu post (hasil Sesi 5)
Route::get('/posts/{id}', [PostController::class, 'show']);

// 👉 TODO (Sesi 6 - Step 2): endpoint bikin post baru.
// Route::post('/posts', [PostController::class, 'store']);

// 👉 TODO (Sesi 6 - Step 4): endpoint like & dislike.
// Route::post('/posts/{id}/like', [PostController::class, 'like']);
// Route::post('/posts/{id}/dislike', [PostController::class, 'dislike']);

// (bawaan Laravel Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
