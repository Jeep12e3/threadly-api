<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // Model "Post" otomatis mewakili tabel "posts" (konvensi Laravel).

    // Kolom yang boleh diisi lewat Post::create([...]) (mass assignment).
    protected $fillable = ['user_id', 'content', 'likes', 'dislikes'];
}
