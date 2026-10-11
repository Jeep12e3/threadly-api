<?php
// ✅ KUNCI JAWABAN Step 7 — routes/api.php
// Tambah route detail /posts/{id}

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);   // ← baru (Step 7)

// (bawaan Laravel Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
