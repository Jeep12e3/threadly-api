<?php
// ✅ KUNCI JAWABAN Step 3 — app/Models/Post.php
// Dibuat lewat: php artisan make:model Post

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // Model "Post" otomatis mewakili tabel "posts" (konvensi Laravel).

    // Kolom yang boleh diisi lewat Post::create([...]).
    protected $fillable = ['user_id', 'content', 'likes', 'dislikes'];
}
