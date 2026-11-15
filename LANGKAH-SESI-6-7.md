# 🧱🔗 Langkah Praktik Sesi 6 + 7 — Middleware & Logic + Full-Stack Integration

> **Titik awal (base branch ini) = kondisi akhir Sesi 5.**
> `GET /api/posts` & `GET /api/posts/{id}` sudah jalan **dari database** (Model `Post`,
> migration, seeder sudah ada). Cek `app/Models/Post.php` & `routes/api.php` — semua sudah ada.
>
> **Target Sesi 6:** API bisa *nerima & nyimpen* data (POST + validasi + middleware).
> **Target Sesi 7:** React (`threadly-react`) consume API ini (CORS + fetch + loading/error).
>
> Materi konsep lengkap ada di `SESI-6-7-MIDDLEWARE-FULLSTACK.md`. File ini = urutan praktiknya.
> Kunci jawaban tiap langkah ada di folder `_solution/`.

---

## 🔧 Persiapan (sekali di awal)

```bash
composer install
cp .env.example .env      # Windows CMD: copy .env.example .env
php artisan key:generate
```

Pastikan **MySQL (XAMPP)** nyala & database `threadly` sudah ada, lalu:
```bash
php artisan migrate --seed
php artisan serve
```
Buka `http://localhost:8000/api/posts` → muncul `{ "posts": [...] }` dari database. ✅
Inilah base Sesi 5 yang bakal kita lanjutkan.

---

# 🧱 SESI 6 — Middleware & Logic

## Step 1 — Kenapa perlu validasi? 🤔

API sekarang baru bisa *ngasih* data (`GET`). Sekarang client mau *ngirim* data
(bikin post). Masalahnya: **data dari client ga boleh dipercaya mentah-mentah** —
bisa kosong, kepanjangan, atau tipe salah. Solusinya: **validasi di server.**

> 💡 Konsep lengkap ada di `SESI-6-7-MIDDLEWARE-FULLSTACK.md` bagian 01.

---

## Step 2 — Endpoint `POST /api/posts` (store) 📥

**2a. Tambah route** di `routes/api.php`:
```php
Route::post('/posts', [PostController::class, 'store']);   // ← baru
```

**2b. Tambah method `store`** di `PostController` (jangan lupa `use Illuminate\Http\Request;`):
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|integer',
        'content' => 'required|string|max:280',
    ]);

    $post = Post::create($validated);

    return response()->json(compact('post'), 201);
}
```

> 💡 `max:280` = sama dengan `MAX_CHARS` di `PostComposer.jsx` (React).
> `likes`/`dislikes` sengaja **tidak** diisi client → pakai `default(0)` dari migration.

*(Kunci: `_solution/PostController.step6.php`, `_solution/routes-api.step6.php`)*

---

## Step 3 — Tes `store` (Thunder Client / Postman) 🧪

`POST` ga bisa dites lewat URL browser. Pakai Thunder Client / Postman.

**Sukses → status 201:**
```
POST http://localhost:8000/api/posts
Content-Type: application/json

{ "user_id": 2, "content": "post pertama lewat API!" }
```

**Gagal (content kosong) → status 422:**
```json
{ "user_id": 2, "content": "" }
```
Response:
```json
{ "message": "The content field is required.",
  "errors": { "content": ["The content field is required."] } }
```

Cek phpMyAdmin → tabel `posts` → baris baru nambah. ✅

---

## Step 4 — Endpoint Like & Dislike ❤️👎

**4a. Tambah route:**
```php
Route::post('/posts/{id}/like',    [PostController::class, 'like']);
Route::post('/posts/{id}/dislike', [PostController::class, 'dislike']);
```

**4b. Tambah method:**
```php
public function like($id)
{
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post tidak ditemukan'], 404);
    }

    $post->increment('likes');

    return response()->json(compact('post'));
}

public function dislike($id)
{
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post tidak ditemukan'], 404);
    }

    $post->increment('dislikes');

    return response()->json(compact('post'));
}
```

Tes: `POST /api/posts/1/like` → `likes` di DB nambah 1. ✅
*(Kunci: `_solution/PostController.step6.php`)*

---

## Step 5 — Middleware (opsional, demo konsep) 🛡️

Middleware = pos pemeriksaan yang dilewati request **sebelum** sampai ke controller.

```bash
php artisan make:middleware LogRequests
```
Isi `handle()`:
```php
public function handle(Request $request, Closure $next): Response
{
    Log::info('API dipanggil: ' . $request->method() . ' ' . $request->path());
    return $next($request);   // teruskan ke controller
}
```
Daftarkan di `bootstrap/app.php` (grup `api`):
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(append: [
        \App\Http\Middleware\LogRequests::class,
    ]);
})
```
Tiap request ke `/api/*` tercatat di `storage/logs/laravel.log`.
*(Kunci: `_solution/LogRequests.php`)*

> 🪜 Step ini murni demo konsep. Boleh di-skip kalau waktu mepet — yang wajib dipahami:
> alur `Request → Middleware → Controller` dan peran `return $next($request)`.

---

# 🔗 SESI 7 — Full-Stack Integration

> Mulai sekarang kita kerja di repo **`threadly-react`** (frontend), sambil API tetap jalan.

## Step 6 — CORS: izinkan React manggil API 🌐

Begitu React (`:5173`) fetch ke Laravel (`:8000`), browser **memblokir** karena beda origin.
Aktifkan CORS di Laravel:

```bash
php artisan config:publish cors
```
Edit `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:5173'],   // ← dev server Vite
'allowed_headers' => ['*'],
```
Restart `php artisan serve`.
*(Kunci: `_solution/cors.php`)*

---

## Step 7 — Base URL API lewat `.env` Vite 🔑

Di repo `threadly-react`, bikin file `.env` (sejajar `package.json`):
```env
VITE_API_URL=http://localhost:8000
```
Dipakai di kode: `import.meta.env.VITE_API_URL`.

> ⚠️ Variabel yang dibaca di browser **wajib** diawali `VITE_`.
> Tambahkan `.env` ke `.gitignore`, commit `.env.example`-nya saja.

*(Kunci: `_solution/env-vite.txt`)*

---

## Step 8 — React: GET feed dari API 📡

Di `src/App.jsx`, ganti data lokal (`import ... posts`) jadi fetch dari API, plus
loading & error state:

```jsx
const [posts, setPosts] = useState([]);
const [loading, setLoading] = useState(true);
const [error, setError] = useState(null);

useEffect(() => {
  fetch(`${import.meta.env.VITE_API_URL}/api/posts`)
    .then((res) => {
      if (!res.ok) throw new Error("Gagal memuat post");
      return res.json();
    })
    .then((data) => {
      // normalisasi: user_id (Laravel) → userId (React)
      const normalized = data.posts.map((post) => ({ ...post, userId: post.user_id }));
      setPosts(normalized);
    })
    .catch((err) => setError(err.message))
    .finally(() => setLoading(false));
}, []);
```

> 🔔 **Reminder penamaan:** Laravel kirim `user_id`, React pakai `userId`. Samakan
> dengan menerjemahkan **sekali** di `App.jsx` (lihat `SESI-6-7-...md` bagian 08).

---

## Step 9 — React: Create post ke API ✍️

Ganti `handleNewPost` supaya kirim `POST` (bukan cuma nambah state lokal):

```jsx
async function handleNewPost(content) {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/api/posts`, {
    method: "POST",
    headers: { "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify({ user_id: currentUser.id, content }),
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.message ?? "Gagal membuat post");

  const newPost = { ...data.post, userId: data.post.user_id };
  setPosts((prev) => [newPost, ...prev]);
}
```

> 💡 **Kirim pakai `user_id`, tampilkan pakai `userId`.** `id` sekarang dari database,
> jadi post **tersimpan permanen** — refresh, tetap ada. 💪

---

## Step 10 — React: Like & Dislike ke API ❤️👎

Ganti `handleLike` / `handleDislike` supaya hit endpoint dan pakai angka dari server:

```jsx
async function handleLike(postId) {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/api/posts/${postId}/like`, {
    method: "POST",
    headers: { Accept: "application/json" },
  });
  const data = await res.json();
  const updated = { ...data.post, userId: data.post.user_id };
  setPosts((posts) => posts.map((p) => (p.id === postId ? updated : p)));
}
```

Tes end-to-end: jalankan `php artisan serve` (API) **dan** `npm run dev` (React),
buka `http://localhost:5173` → login → bikin post → refresh → post tetap ada. 🎉
*(Kunci: `_solution/App.jsx`)*

> 🔐 **Login tetap lokal** di sesi ini (array `users` React). Login lewat API butuh
> autentikasi token → materi sesi lanjutan. Lihat `SESI-6-7-...md` bagian 12.

---

## ✅ Ringkasan Alur

```
Akhir Sesi 5:  GET /posts & /posts/{id} → DB → JSON        (cuma bisa BACA)
                     │
   Step 2–3: POST /posts + validasi      (bisa TULIS, 201/422)
   Step 4  : like & dislike              (ubah angka di DB)
   Step 5  : middleware (opsional)       (gerbang request)
   Step 6  : CORS                        (React boleh manggil)
   Step 7  : .env Vite                   (base URL API)
   Step 8  : React GET feed              (ganti data lokal → API)
   Step 9  : React create post           (POST → DB)
   Step 10 : React like/dislike          (POST → DB)
                     ↓
Akhir Sesi 7:  React ⟷ Laravel ⟷ Database  (web beneran fungsional) 🥳
```

> 💡 Peta lengkap + penjelasan konsep tiap bagian: `SESI-6-7-MIDDLEWARE-FULLSTACK.md`.
