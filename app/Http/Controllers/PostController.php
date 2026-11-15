<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // GET /api/posts → semua post (hasil Sesi 5)
    public function index()
    {
        $posts = Post::all();

        return response()->json(compact('posts'));
    }

    // GET /api/posts/{id} → satu post (hasil Sesi 5)
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        return response()->json(compact('post'));
    }

    // 👉 TODO (Sesi 6 - Step 2 & 3): POST /api/posts — bikin post baru.
    //    1. Validasi request: user_id (required|integer), content (required|string|max:280).
    //    2. Simpan pakai Post::create($validated).
    //    3. Balikin post yang dibuat + status 201.
    //    (Kunci: _solution/PostController.step6.php)
    //
    // public function store(Request $request)
    // {
    //     ...
    // }

    // 👉 TODO (Sesi 6 - Step 4): POST /api/posts/{id}/like — tambah like.
    //    - cari post (find), kalau tidak ada → 404
    //    - $post->increment('likes'); lalu balikin post-nya.
    //
    // public function like($id)
    // {
    //     ...
    // }

    // 👉 TODO (Sesi 6 - Step 4): POST /api/posts/{id}/dislike — tambah dislike.
    //
    // public function dislike($id)
    // {
    //     ...
    // }
}
