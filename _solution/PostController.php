<?php
// ✅ KUNCI JAWABAN Step 6 (FINAL) — app/Http/Controllers/PostController.php
// Tahap kedua: rapikan output pakai compact('posts') → { "posts": [...] }

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return response()->json(compact('posts'));
    }
}

// --- Perjalanan controller ---
// Sesi 4        : $posts = [ [...], [...] ];          → json($posts)          (hardcode, array)
// Sesi 5 Step 5 : $posts = Post::all();               → json($posts)          (database, array)
// Sesi 5 Step 6 : $posts = Post::all();               → json(compact('posts')) (database, { "posts": [...] })
