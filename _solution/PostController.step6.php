<?php
// ✅ KUNCI JAWABAN Sesi 6 — app/Http/Controllers/PostController.php
// index() & show() = hasil Sesi 5 (tetap).
// store(), like(), dislike() = tambahan Sesi 6.

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

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

    // POST /api/posts → bikin post baru (Sesi 6 - Step 2 & 3)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'content' => 'required|string|max:280',
        ]);

        $post = Post::create($validated);

        return response()->json(compact('post'), 201);
    }

    // POST /api/posts/{id}/like → tambah like (Sesi 6 - Step 4)
    public function like($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->increment('likes');

        return response()->json(compact('post'));
    }

    // POST /api/posts/{id}/dislike → tambah dislike (Sesi 6 - Step 4)
    public function dislike($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->increment('dislikes');

        return response()->json(compact('post'));
    }
}
