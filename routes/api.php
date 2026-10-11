<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// Semua route di sini otomatis diawali /api

// GET /api/posts → semua post (dibuat di Sesi 4)
Route::get('/posts', [PostController::class, 'index']);

// (bawaan Laravel Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
