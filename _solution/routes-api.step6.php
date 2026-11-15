<?php
// ✅ KUNCI JAWABAN Sesi 6 — routes/api.php
// Tambah POST /posts (store) + like & dislike.

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

Route::post('/posts', [PostController::class, 'store']);                    // ← baru (Step 2)
Route::post('/posts/{id}/like', [PostController::class, 'like']);          // ← baru (Step 4)
Route::post('/posts/{id}/dislike', [PostController::class, 'dislike']);    // ← baru (Step 4)

// (bawaan Laravel Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
