<?php
// ✅ KUNCI JAWABAN Step 2 — database/migrations/xxxx_create_posts_table.php
// Dibuat lewat: php artisan make:migration create_posts_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();                            // id (auto increment)
            $table->integer('user_id');              // id penulis post
            $table->text('content');                 // isi post
            $table->integer('likes')->default(0);    // jumlah like
            $table->integer('dislikes')->default(0); // jumlah dislike
            $table->timestamps();                    // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
