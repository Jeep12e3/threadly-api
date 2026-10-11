<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    /**
     * GET /api/posts
     * Ambil semua post dari database.
     */
    public function index()
    {
        $posts = Post::all();

        // compact('posts') → ['posts' => $posts]
        // Hasil JSON: { "posts": [...] }  (bentuknya sama seperti DummyJSON)
        return response()->json(compact('posts'));
    }

    /**
     * GET /api/posts/{id}
     * Ambil satu post berdasarkan id.
     */
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        return response()->json(compact('post'));
    }
}
