# 🔑 _solution — Kunci Jawaban Sesi 5 (Database Integration)

Folder ini **BUKAN bagian aplikasi** (tidak dipakai Laravel). Isinya versi **jadi** dari tiap langkah Sesi 5,
untuk referensi mentor / cek jawaban peserta.

> Panduan langkah ada di `../LANGKAH-SESI-5.md`.

## Titik awal (base) = akhir Sesi 4
- `app/Http/Controllers/PostController.php` → `index()` masih **hardcode**.
- **Belum ada** Model `Post`, migration `posts`, maupun `PostSeeder` (peserta generate sendiri via artisan).

## Urutan langkah & file jawaban

| Step | Yang dikerjakan peserta | File jawaban di sini |
|---|---|---|
| 1 | Set `.env` ke MySQL + bikin DB `threadly` | `env-snippet.txt` |
| 2 | `php artisan make:migration` → isi kolom | `create_posts_table.php` |
| 3 | `php artisan make:model Post` → isi `$fillable` | `Post.php` |
| 4 | `php artisan make:seeder PostSeeder` → isi data + daftarkan di DatabaseSeeder | `PostSeeder.php`, `DatabaseSeeder.php` |
| 5 | Ubah `PostController@index`: hardcode → `Post::all()` | `PostController.php` |

## Perintah yang dijalankan peserta (urut)
```bash
# Step 2
php artisan make:migration create_posts_table   # lalu isi file-nya
php artisan migrate

# Step 3
php artisan make:model Post                      # lalu isi $fillable

# Step 4
php artisan make:seeder PostSeeder               # lalu isi data
php artisan db:seed --class=PostSeeder

# Step 5 → edit PostController, lalu tes:
php artisan serve
# buka http://localhost:8000/api/posts
```

> 💡 Shortcut: `php artisan make:model Post -m` bikin Model + migration sekaligus (Step 2 & 3 jadi satu).
