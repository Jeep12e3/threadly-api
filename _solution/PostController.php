<?php
// ✅ KUNCI JAWABAN Step 5 — app/Http/Controllers/PostController.php
// Ubah dari HARDCODE (akhir Sesi 4) → ambil dari DATABASE.

namespace App\Http\Controllers;

use App\Models\Post;   // ← import Model (baru)

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();   // ambil semua post dari database

        return response()->json(compact('posts'));
    }
}

// --- Perbandingan ---
// SEBELUM (Sesi 4):  $posts = [ [...], [...] ];   // array hardcode
// SESUDAH (Sesi 5):  $posts = Post::all();        // dari database
// Bagian `return response()->json(compact('posts'))` TIDAK berubah.
