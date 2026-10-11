<?php
// ✅ KUNCI JAWABAN FINAL — app/Http/Controllers/PostController.php
// Step 6: index() rapikan output pakai compact('posts')
// Step 7: tambah show($id) untuk detail 1 post (+ 404)

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    // GET /api/posts → semua post
    public function index()
    {
        $posts = Post::all();

        return response()->json(compact('posts'));
    }

    // GET /api/posts/{id} → satu post
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        return response()->json(compact('post'));
    }
}

// --- Perjalanan controller ---
// Sesi 4        : $posts = [ [...], [...] ];   → json($posts)            (hardcode, array)
// Sesi 5 Step 5 : $posts = Post::all();        → json($posts)            (database, array)
// Sesi 5 Step 6 : $posts = Post::all();        → json(compact('posts'))  (database, { "posts": [...] })
// Sesi 5 Step 7 : + show($id) → find($id) + 404 → json(compact('post'))  (detail 1 post)
