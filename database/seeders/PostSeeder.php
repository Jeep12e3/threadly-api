<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Data awal — sama dengan src/data/posts.js di Threadly React.
     * Jalankan: php artisan db:seed --class=PostSeeder
     */
    public function run(): void
    {
        Post::create(['user_id' => 2, 'content' => 'lagi belajar React ternyata hooks seru juga 😭', 'likes' => 12, 'dislikes' => 1]);
        Post::create(['user_id' => 3, 'content' => 'ada yang mau belajar React bareng?', 'likes' => 8, 'dislikes' => 0]);
        Post::create(['user_id' => 4, 'content' => 'hari ini akhirnya selesai bikin component pertama', 'likes' => 15, 'dislikes' => 2]);
        Post::create(['user_id' => 5, 'content' => 'kenapa CSS selalu bikin layout berantakan 😭', 'likes' => 20, 'dislikes' => 3]);
    }
}
