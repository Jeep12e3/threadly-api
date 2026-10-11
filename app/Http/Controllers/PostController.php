<?php

namespace App\Http\Controllers;

class PostController extends Controller
{
    /**
     * GET /api/posts
     *
     * ⬅️ Ini kondisi akhir SESI 4: datanya masih HARDCODE di sini.
     *
     * 🎯 TARGET SESI 5: ganti data hardcode ini supaya diambil dari DATABASE.
     *    (lihat LANGKAH-SESI-5.md — nanti method ini pakai Post::all())
     */
    public function index()
    {
        $posts = [
            ['id' => 1, 'user_id' => 2, 'content' => 'lagi belajar React ternyata hooks seru juga 😭', 'likes' => 12, 'dislikes' => 1],
            ['id' => 2, 'user_id' => 3, 'content' => 'ada yang mau belajar React bareng?', 'likes' => 8, 'dislikes' => 0],
            ['id' => 3, 'user_id' => 4, 'content' => 'hari ini akhirnya selesai bikin component pertama', 'likes' => 15, 'dislikes' => 2],
            ['id' => 4, 'user_id' => 5, 'content' => 'kenapa CSS selalu bikin layout berantakan 😭', 'likes' => 20, 'dislikes' => 3],
        ];

        return response()->json(compact('posts'));
    }
}
