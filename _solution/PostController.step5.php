<?php
// ✅ KUNCI JAWABAN Step 5 — app/Http/Controllers/PostController.php
// Tahap pertama: ganti HARDCODE (akhir Sesi 4) → ambil dari DATABASE.
// Output masih array langsung: [ {...}, {...} ]  (dirapikan di Step 6)

namespace App\Http\Controllers;

use App\Models\Post;   // ← import Model (baru)

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();   // ambil semua post dari database

        return response()->json($posts);
    }
}
