<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// Semua route di sini otomatis diawali /api

// GET /api/posts        → semua post
Route::get('/posts', [PostController::class, 'index']);

// GET /api/posts/{id}   → satu post
Route::get('/posts/{id}', [PostController::class, 'show']);

// (bawaan Laravel Sanctum — dibiarkan untuk referensi auth nanti)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
