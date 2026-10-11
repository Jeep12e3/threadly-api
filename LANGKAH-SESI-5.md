# 🗄️ Langkah Praktik Sesi 5 — Database Integration

> **Titik awal (base ini) = kondisi akhir Sesi 4.**
> API `GET /api/posts` sudah jalan, tapi datanya masih **hardcode** di `PostController`.
> **Target Sesi 5:** pindahkan data itu ke **database**.
>
> Materi konsep lengkap ada di `SESI-5-DATABASE-INTEGRATION.md`. File ini = urutan praktiknya.
> Kunci jawaban tiap langkah ada di folder `_solution/`.

---

## 🔧 Persiapan (sekali di awal)

```bash
composer install
cp .env.example .env      # Windows CMD: copy .env.example .env
php artisan key:generate
```

Cek base jalan dulu:
```bash
php artisan serve
```
Buka `http://localhost:8000/api/posts` → muncul JSON `{ "posts": [...] }` (data hardcode). ✅
Inilah yang bakal kita ubah jadi "dari database".

---

## Step 1 — Hubungkan ke MySQL 🔌

1. Nyalakan **MySQL** di XAMPP Control Panel.
2. Buka `http://localhost/phpmyadmin` → tab **Databases** → buat database **`threadly`**.
3. Edit `.env` bagian database (lihat `_solution/env-snippet.txt`):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=threadly
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > ⚠️ Default-nya `DB_CONNECTION=sqlite` — WAJIB ganti ke `mysql`.

---

## Step 2 — Buat tabel `posts` lewat Migration 📐

```bash
php artisan make:migration create_posts_table
```
Buka file baru di `database/migrations/`, isi method `up()`:
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->integer('user_id');
    $table->text('content');
    $table->integer('likes')->default(0);
    $table->integer('dislikes')->default(0);
    $table->timestamps();
});
```
Jalankan:
```bash
php artisan migrate
```
Cek phpMyAdmin → database `threadly` → tabel **`posts`** muncul. ✅
*(Kunci: `_solution/create_posts_table.php`)*

---

## Step 3 — Buat Model `Post` 🧩

```bash
php artisan make:model Post
```
Isi `app/Models/Post.php`:
```php
protected $fillable = ['user_id', 'content', 'likes', 'dislikes'];
```
> 💡 Model `Post` otomatis mewakili tabel `posts`.
> 💡 Shortcut: `php artisan make:model Post -m` → bikin Model + migration sekaligus (gabung Step 2 & 3).

*(Kunci: `_solution/Post.php`)*

---

## Step 4 — Isi data awal lewat Seeder 🌱

```bash
php artisan make:seeder PostSeeder
```
Isi `database/seeders/PostSeeder.php` (data sama dgn `posts.js` di React):
```php
Post::create(['user_id' => 2, 'content' => 'lagi belajar React ternyata hooks seru juga 😭', 'likes' => 12, 'dislikes' => 1]);
Post::create(['user_id' => 3, 'content' => 'ada yang mau belajar React bareng?', 'likes' => 8, 'dislikes' => 0]);
Post::create(['user_id' => 4, 'content' => 'hari ini akhirnya selesai bikin component pertama', 'likes' => 15, 'dislikes' => 2]);
Post::create(['user_id' => 5, 'content' => 'kenapa CSS selalu bikin layout berantakan 😭', 'likes' => 20, 'dislikes' => 3]);
```
Daftarkan di `database/seeders/DatabaseSeeder.php` (sudah ada TODO-nya):
```php
$this->call(PostSeeder::class);
```
Jalankan:
```bash
php artisan db:seed --class=PostSeeder
```
Cek phpMyAdmin → tabel `posts` → tab **Browse** → 4 baris data muncul. ✅
*(Kunci: `_solution/PostSeeder.php`, `_solution/DatabaseSeeder.php`)*

---

## Step 5 — Controller ambil dari Database 🎮

Ubah `app/Http/Controllers/PostController.php` — ganti data hardcode jadi `Post::all()`:

```php
use App\Models\Post;   // ← tambahkan import ini di atas

public function index()
{
    $posts = Post::all();   // ← ganti dari array hardcode

    return response()->json($posts);
}
```

Tes:
```bash
php artisan serve
```
Buka `http://localhost:8000/api/posts` → data sekarang **dari database**. 🎉

**Buktikan:** tambah 1 baris lewat phpMyAdmin → refresh browser → post baru langsung ikut muncul, **tanpa edit kode**. 💪
*(Kunci: `_solution/PostController.step5.php`)*

---

## Step 6 — Rapikan output pakai `compact()` 📦

Sekarang hasilnya masih array langsung: `[ {...}, {...} ]`.
Kita rapikan jadi `{ "posts": [...] }` pakai `compact('posts')`:

```php
public function index()
{
    $posts = Post::all();

    return response()->json(compact('posts'));   // ← bungkus pakai compact
}
```

`compact('posts')` = `['posts' => $posts]`, jadi JSON-nya berubah:

```
Sebelum (Step 5):  [ { "id":1, ... }, { "id":2, ... } ]
Sesudah (Step 6):  { "posts": [ { "id":1, ... }, ... ] }
```

> 💡 **Kenapa dibungkus?** Biar bentuknya sama dengan DummyJSON di Sesi 3 (`{ "posts": [...] }`).
> Nanti di Sesi 7, React cukup ganti URL-nya, `setPosts(data.posts)` tetap dipakai. 🎯

*(Kunci: `_solution/PostController.php`)*

---

## Step 7 — Ambil satu post (Detail) 🔍

Selain semua post, API biasanya punya endpoint **satu post** berdasarkan `id`.

**7a. Tambah route** di `routes/api.php`:
```php
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);   // ← baru
```
`{id}` = **parameter**. Akses `/api/posts/2` → `$id = 2`.
> 💡 Mirip `useParams()` di React Router — bagian URL yang bisa berubah-ubah.

**7b. Tambah method `show`** di `PostController`:
```php
public function show($id)
{
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post tidak ditemukan'], 404);
    }

    return response()->json(compact('post'));
}
```

Tes:
| Akses | Hasil |
|---|---|
| `/api/posts/2` | `{ "post": { "id": 2, ... } }` — status 200 |
| `/api/posts/999` | `{ "message": "Post tidak ditemukan" }` — status **404** |

> 💡 Di sinilah gunanya `response()->json()` versi eksplisit (dari Sesi 4) — biar bisa kirim status **404**.

*(Kunci: `_solution/PostController.php` & `_solution/routes-api.php`)*

---

## ✅ Ringkasan Alur

```
Akhir Sesi 4:  Controller → array HARDCODE → JSON
                     │
   Step 1: .env → MySQL          (sambungkan)
   Step 2: migration             (bikin tabel posts)
   Step 3: model Post            (jembatan ke tabel)
   Step 4: seeder                (isi data awal)
   Step 5: Post::all()           (controller baca database → json($posts))
   Step 6: compact('posts')      (rapikan output → { "posts": [...] })
   Step 7: show($id)             (detail 1 post + 404 → /posts/{id})
                     ↓
Akhir Sesi 5:  Controller → DATABASE → JSON { "posts": [...] } + detail /posts/{id}
```

> 💡 Yang berubah di controller cuma **sumber datanya** (`[...]` → `Post::all()`),
> lalu **bentuk output-nya** dirapikan pakai `compact()`. Route tetap sama persis seperti Sesi 4.
