# SESI-6-7-MIDDLEWARE-FULLSTACK

## 🧱 Middleware & Logic + 🔗 Full-Stack Integration — Sesi 6 & 7

**Topic:** Middleware & Logic (Sesi 6) + Full-Stack Integration (Sesi 7)
**Details:** Data Validation, Middleware, CORS Configuration, API Consumption, State & Error Handling (React)
**Project:** Threadly (backend `threadly-api` + frontend `threadly-react`)
**Duration:** ±3–4 jam (2 sesi digabung)
**Tools:** Laravel (PHP) + MySQL (XAMPP) · React (Vite)

> 📦 Catatan ini **menggabungkan 2 sesi** jadi satu alur:
> **Sesi 6** bikin API-nya bisa *nerima & nyimpen* data (POST + validasi + middleware),
> **Sesi 7** nyambungin React ke API itu (CORS + fetch + loading/error state).
>
> Kunci jawaban tiap langkah ada di folder `_solution/`.

---

## 🎯 Learning Objectives

Setelah sesi ini, peserta diharapkan mampu:

**Sesi 6 — Middleware & Logic**
- Memahami kenapa data dari client **tidak boleh dipercaya mentah-mentah**
- Membuat endpoint `POST /api/posts` untuk **menyimpan** data baru
- Melakukan **validasi** input (wajib diisi, batas panjang, tipe benar)
- Memahami **HTTP status code** untuk sukses (`201`) & gagal (`422`)
- Memahami apa itu **Middleware** & kapan dipakai
- Menambah endpoint **like / dislike** yang mengubah data di database

**Sesi 7 — Full-Stack Integration**
- Memahami apa itu **CORS** & kenapa browser memblokir request antar-origin
- Mengaktifkan CORS di Laravel agar React boleh memanggil API
- Mengganti **mock data lokal** di React dengan data **dari API** (`fetch`)
- Mengelola **loading state** & **error state** di React
- Membuat alur **create post** & **like/dislike** benar-benar tersimpan di database

---

## 🧭 Di Mana Kita Sekarang?

Rekap perjalanan:

```
Sesi 1  → React: Component, JSX, Props
Sesi 2  → React: Hooks (useState, useEffect, useRef)
Sesi 3  → React: Mock Data, Fetch (DummyJSON), Routing
Sesi 4  → Laravel: API Fundamentals (Route → Controller → JSON hardcode)
Sesi 5  → Laravel: Database (Migration, Model, compact(), GET /posts & /posts/{id})
Sesi 6  → Laravel: POST /posts + Validasi + Middleware        ← kita di sini
Sesi 7  → React ⟷ Laravel: CORS + consume API + state         ← lalu ke sini
```

Sampai Sesi 5, API kita **cuma bisa ngasih data** (`GET`). React juga masih
hidup sendiri dengan data di `src/data/posts.js`. Dua dunia yang belum ketemu. 🌉

```
SEKARANG:
  React  →  src/data/posts.js   (data lokal, hilang saat refresh)
  Laravel →  GET /api/posts      (data DB, tapi belum ada yang manggil)

TARGET SESI 6+7:
  React  →  fetch(API)  →  Laravel  →  Database   (satu alur, data beneran)
```

> 💡 **Ingat prasyarat:** catatan ini melanjutkan **hasil akhir Sesi 5**.
> Pastikan `Post` (Model), migration `posts`, dan `PostSeeder` sudah ada,
> `GET /api/posts` & `GET /api/posts/{id}` sudah jalan dari database.

---

# 🧱 BAGIAN A — SESI 6: Middleware & Logic

## 01. 🤔 Kenapa Perlu Validasi?

Sampai sekarang API cuma **ngasih** data. Sekarang client mau **ngirim** data
(bikin post baru). Masalahnya: **data dari client tidak bisa dipercaya.**

Bayangin ada yang kirim:

| Yang dikirim | Masalah |
|---|---|
| `content` kosong | post hampa, ga ada isinya |
| `content` 5000 huruf | kepanjangan, berantakan |
| `user_id` berupa teks `"haha"` | tipe salah, DB bisa error |
| field `likes` diisi `99999` | curang — like harusnya mulai dari 0 |

> ⚠️ **Aturan emas backend:** *"Never trust the client."*
> Frontend boleh saja punya validasi (mis. tombol disable kalau kosong),
> tapi itu gampang dilewati. **Validasi di server itu wajib.**

Analogi: validasi frontend = satpam di pintu (ramah, bisa dihindari lewat jendela).
Validasi backend = brankas (yang beneran ngamanin). 🔐

---

## 02. 📥 Endpoint Baru: `POST /api/posts` (Store)

Sampai Sesi 5 kita punya:

```php
Route::get('/posts', [PostController::class, 'index']);       // semua post
Route::get('/posts/{id}', [PostController::class, 'show']);   // satu post
```

Sekarang tambah route **POST** untuk bikin post baru. Konvensi nama method-nya: `store`.

```php
Route::post('/posts', [PostController::class, 'store']);      // ← baru (Sesi 6)
```

| Method | URL | Method Controller | Guna |
|---|---|---|---|
| GET | `/api/posts` | `index` | ambil semua post |
| GET | `/api/posts/{id}` | `show` | ambil satu post |
| **POST** | **`/api/posts`** | **`store`** | **bikin post baru** |

> 💡 **GET vs POST:** `GET` cuma *minta* data (ga ngubah apa-apa).
> `POST` *mengirim* data buat *disimpan*. Makanya ga bisa dites lewat URL browser biasa —
> harus pakai **Postman / Thunder Client** (ingat Sesi 4).

---

## 03. ✅ Validasi di Controller

Laravel punya validasi bawaan lewat `$request->validate([...])`. Kalau data **lolos**,
kode lanjut. Kalau **gagal**, Laravel otomatis balikin JSON error + status **422** —
kita ga perlu nulis `if` manual.

Isi method `store` di `app/Http/Controllers/PostController.php`:

```php
use Illuminate\Http\Request;   // ← tambahkan import ini di atas

public function store(Request $request)
{
    // 1. Validasi dulu
    $validated = $request->validate([
        'user_id' => 'required|integer',
        'content' => 'required|string|max:280',
    ]);

    // 2. Kalau lolos, simpan ke database
    $post = Post::create($validated);

    // 3. Balikin post yang baru dibuat + status 201 (Created)
    return response()->json(compact('post'), 201);
}
```

Perhatikan:
- `$request->validate([...])` **mengembalikan** data yang sudah bersih (`$validated`).
  Jadi kita simpan yang sudah tervalidasi, bukan `$request->all()` mentah.
- `likes` & `dislikes` **tidak** diisi client — biar pakai `default(0)` dari migration.
  Ini mencegah orang curang set `likes => 99999`.
- Status **`201`** artinya "berhasil **membuat** data" (beda dari `200` biasa).

### Aturan validasi yang sering dipakai

| Rule | Arti |
|---|---|
| `required` | wajib ada & tidak kosong |
| `string` | harus berupa teks |
| `integer` | harus angka bulat |
| `max:280` | maksimal 280 karakter (kalau string) |
| `min:1` | minimal 1 karakter |
| `nullable` | boleh kosong |

> 💡 **Kenapa `max:280`?** Biar **sama persis** dengan `MAX_CHARS = 280` di
> `src/components/PostComposer.jsx` (React). Frontend & backend sepakat angka yang sama. 🎯

---

## 04. 🧪 Tes Endpoint `store` (Thunder Client / Postman)

**Request sukses:**

```
POST http://localhost:8000/api/posts
Content-Type: application/json

{
  "user_id": 2,
  "content": "post pertama lewat API!"
}
```

Response — **status 201**:

```json
{
  "post": {
    "id": 5,
    "user_id": 2,
    "content": "post pertama lewat API!",
    "likes": 0,
    "dislikes": 0,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

**Request gagal (content kosong):**

```json
{ "user_id": 2, "content": "" }
```

Response — **status 422**:

```json
{
  "message": "The content field is required.",
  "errors": {
    "content": ["The content field is required."]
  }
}
```

> 💡 Nanti di Sesi 7, bentuk `errors` ini yang dibaca React buat nampilin pesan
> error ke user. Makanya penting formatnya konsisten.

---

## 05. ❤️👎 Endpoint Like & Dislike

Di React (Sesi 2), like/dislike cuma ngubah **state lokal** — hilang saat refresh.
Sekarang kita bikin biar **tersimpan di database**.

Tambah route:

```php
Route::post('/posts/{id}/like',    [PostController::class, 'like']);
Route::post('/posts/{id}/dislike', [PostController::class, 'dislike']);
```

Method di controller:

```php
public function like($id)
{
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post tidak ditemukan'], 404);
    }

    $post->increment('likes');   // likes = likes + 1, langsung tersimpan

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

> 💡 `increment('likes')` = cara singkat Eloquent buat nambah 1 & langsung simpan.
> Sama dengan `$post->likes = $post->likes + 1; $post->save();`.

> ⚠️ **Penyederhanaan materi:** di sini 1 klik = +1 selamanya (belum ada "batal like"
> atau "1 user 1 reaksi"). Logika toggle like↔dislike tetap diurus di React (state
> `reactions`), seperti yang sudah ada. Versi "benar" (1 user 1 reaksi) butuh tabel
> relasi + auth → itu materi lanjutan, lihat **Bonus**.

---

## 06. 🛡️ Apa itu Middleware?

**Middleware** = lapisan yang dilewati request **sebelum** sampai ke Controller.
Ibarat **pos pemeriksaan** di gerbang sebelum tamu masuk ke dalam. 🚧

```
Request  →  [ Middleware ]  →  Controller  →  Response
             ↑ dicek di sini dulu
```

Contoh middleware bawaan Laravel yang mungkin sudah kamu pakai tanpa sadar:
- `auth` → cek apakah user sudah login (dipakai di route `/user` bawaan Sanctum)
- `throttle` → batasi jumlah request (anti-spam)

Middleware cocok buat hal yang **berlaku untuk banyak route sekaligus**, misalnya:
logging, cek login, rate-limit, CORS (nanti di Sesi 7 — CORS juga sebenarnya middleware!).

### Contoh kecil: middleware logging sederhana

Bikin middleware:

```bash
php artisan make:middleware LogRequests
```

Isi `app/Http/Middleware/LogRequests.php` bagian `handle`:

```php
public function handle(Request $request, Closure $next): Response
{
    // dijalankan SEBELUM controller
    Log::info('API dipanggil: ' . $request->method() . ' ' . $request->path());

    return $next($request);   // teruskan ke controller
}
```

Daftarkan di `bootstrap/app.php` (hanya untuk grup `api`):

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(append: [
        \App\Http\Middleware\LogRequests::class,
    ]);
})
```

Sekarang tiap request ke `/api/*` otomatis tercatat di `storage/logs/laravel.log`.

> 💡 **Konsep inti middleware:** `return $next($request)` = "lanjutkan ke tahap berikutnya".
> Kalau middleware memutuskan **menolak** (mis. belum login), dia `return response()->json(..., 401)`
> **tanpa** memanggil `$next()` — request dihentikan di gerbang.

> 🪜 **Opsional:** middleware logging ini murni demo konsep. Boleh di-skip kalau waktu mepet.
> Yang **wajib** dipahami: alur `Request → Middleware → Controller` dan peran `$next`.

---

# 🔗 BAGIAN B — SESI 7: Full-Stack Integration

## 07. 🌐 Masalah Pertama: CORS

Begitu React (`http://localhost:5173`) coba `fetch` ke Laravel
(`http://localhost:8000`), browser langsung **memblokir**:

```
Access to fetch at 'http://localhost:8000/api/posts' from origin
'http://localhost:5173' has been blocked by CORS policy.
```

**Kenapa?** Keduanya **origin berbeda** (beda port = beda origin). Demi keamanan,
browser melarang halaman dari origin A memanggil API di origin B — **kecuali** server
B secara eksplisit bilang "boleh".

```
http://localhost:5173   (React/Vite)   ─╳─►  http://localhost:8000  (Laravel)
                         diblokir browser karena beda origin
```

> 💡 **CORS = Cross-Origin Resource Sharing.** Ini aturan **browser**, bukan error kodemu.
> Postman/Thunder Client ga kena CORS (bukan browser) — makanya tes API kemarin aman,
> tapi begitu dari React baru muncul masalahnya.

### Mengaktifkan CORS di Laravel

Publish config CORS (kalau belum ada):

```bash
php artisan config:publish cors
```

Buka `config/cors.php`, pastikan origin React diizinkan:

```php
'paths' => ['api/*'],

'allowed_methods' => ['*'],

'allowed_origins' => [
    'http://localhost:5173',   // ← alamat dev server Vite
],

'allowed_headers' => ['*'],
```

Restart `php artisan serve`. Sekarang browser dari `5173` boleh manggil API `8000`. ✅

> ⚠️ `allowed_origins => ['*']` (izinkan semua) hanya buat latihan. Di produksi,
> sebutkan **domain frontend yang spesifik** saja.

---

## 08. ⚠️ Reminder: Pastikan Nama Variabel React & Laravel Cocok

> 🔔 **Checkpoint sebelum lanjut:** sebelum React consume API, pastikan **nama field
> yang dipakai React sama persis dengan yang dikirim Laravel.** Ini sumber bug
> "data ada tapi ga muncul" yang paling sering kejadian pas full-stack.

Di Threadly, ada satu field yang namanya beda di dua sisi:

| | Backend (Laravel/DB) | Frontend (React) |
|---|---|---|
| Nama field penulis | `user_id` (snake_case) | `userId` (camelCase) |
| Dipakai di | tabel `posts`, JSON API | `post.userId` di beberapa komponen |

Kalau dibiarkan beda, React nyari `post.userId` tapi API ngasih `post.user_id` →
hasilnya `undefined` → avatar & nama penulis ga muncul. Padahal datanya **ada**.

> 💡 **Kenapa beda?** Laravel ngikut konvensinya sendiri (`user_id`, snake_case),
> React ngikut konvensi JS (`userId`, camelCase). Dua-duanya "benar" di dunianya
> masing-masing — tugas kita cuma **menyambungkannya**.

**Cara menyamakannya di sini: React yang menyesuaikan.** Backend tetap `user_id`
(konvensi Laravel, jangan diubah). React cukup **menerjemahkan sekali** saat data masuk.

### Cukup satu tempat (di `App.jsx`)

`post.userId` dipakai di beberapa file (`HomePage.jsx`, `ProfilePage.jsx`). Daripada
ganti nama satu-satu di tiap komponen, lebih rapi **mengganti nama field tepat saat
data masuk dari API** — di `App.jsx`, di dalam `fetch`. Setelah diubah di pintu masuk,
seluruh aplikasi tetap melihat `userId` seperti sebelumnya.

```jsx
// src/App.jsx — di dalam .then() setelah data diterima dari API
.then((data) => {
  // ubah user_id (dari API) → userId (yang dipakai komponen React)
  const normalized = data.posts.map((post) => ({
    ...post,
    userId: post.user_id,
  }));
  setPosts(normalized);
})
```

Dengan cara ini:
- `HomePage.jsx` tetap pakai `post.userId` → **tidak disentuh**
- `ProfilePage.jsx` tetap pakai `p.userId` → **tidak disentuh**
- Semua "penerjemahan" ada di **satu titik** yang gampang dicari.

> 💡 **Istilahnya "normalisasi data":** membereskan bentuk data dari luar jadi bentuk
> yang dipakai aplikasi, tepat di titik data itu masuk. Pola ini umum dipakai kalau
> bentuk dari API beda sedikit dengan yang diharapkan komponen.

> 🧠 **Catatan untuk create post (bagian 10):** waktu kita *membaca* data, React
> menerjemahkan `user_id` → `userId`. Tapi waktu *mengirim* post baru, body request
> tetap memakai `user_id` (karena itu yang diminta validasi backend). Jadi: **baca pakai
> `userId`, kirim pakai `user_id`.**

---

## 09. 📡 React Consume API — GET Feed

Inilah inti Sesi 7. Kita ganti data lokal jadi data dari API.

### Cara LAMA (lokal — sebelum Sesi 7)

```jsx
// src/App.jsx
import { posts as initialPosts } from "./data/posts";

const [posts, setPosts] = useState(initialPosts);   // data dari file JS
```

### Cara BARU (dari API — Sesi 7)

```jsx
// src/App.jsx
import { useState, useEffect } from "react";

const [posts, setPosts] = useState([]);       // mulai kosong
const [loading, setLoading] = useState(true); // lagi ambil data?
const [error, setError] = useState(null);     // ada error?

useEffect(() => {
  fetch(`${import.meta.env.VITE_API_URL}/api/posts`)
    .then((res) => {
      if (!res.ok) throw new Error("Gagal memuat post");
      return res.json();
    })
    .then((data) => {
      // normalisasi: user_id (API) → userId (React) — lihat bagian 08
      const normalized = data.posts.map((post) => ({
        ...post,
        userId: post.user_id,
      }));
      setPosts(normalized);
    })
    .catch((err) => setError(err.message))
    .finally(() => setLoading(false));
}, []); // [] → jalan sekali saat halaman pertama dibuka
```

> 💡 **Lihat betapa miripnya dengan Sesi 3 (DummyJSON)!** Yang berubah cuma URL-nya.
> Berkat `compact('posts')` di Sesi 5, bentuk responnya tetap `{ "posts": [...] }` —
> jadi kamu membaca `data.posts` persis seperti waktu pakai DummyJSON. Satu-satunya
> tambahan: menerjemahkan `user_id` → `userId` (bagian 08). Ini buah dari perencanaan. 🎯

```
Sesi 3:  fetch("https://dummyjson.com/posts")        → data.posts
Sesi 7:  fetch("http://localhost:8000/api/posts")    → data.posts  (sama!)
```

### Base URL lewat `.env` Vite (seperti di dunia nyata)

Jangan hardcode `http://localhost:8000` di banyak tempat. Pakai environment variable.

Bikin file `.env` di root `threadly-react`:

```env
VITE_API_URL=http://localhost:8000
```

Lalu dipakai: `import.meta.env.VITE_API_URL`.

> ⚠️ **Aturan Vite:** variabel yang mau dibaca di browser **wajib** diawali `VITE_`.
> Variabel tanpa prefix itu tidak akan ter-expose ke kode frontend.
> Jangan lupa tambah `.env` ke `.gitignore`, dan sediakan `.env.example` buat tim.

### Menampilkan loading & error

```jsx
if (loading) return <p>Loading...</p>;
if (error)   return <p>⚠️ {error}</p>;

// ... baru render feed
```

---

## 10. ✍️ React Consume API — Create Post

### Cara LAMA (lokal)

```jsx
function handleNewPost(content) {
  const newPost = { id: Date.now(), userId: currentUser.id, content, likes: 0, dislikes: 0 };
  setPosts((prev) => [newPost, ...prev]);   // cuma nambah di state, hilang saat refresh
}
```

### Cara BARU (ke API → tersimpan di DB)

```jsx
async function handleNewPost(content) {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/api/posts`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",   // biar Laravel balikin error sbg JSON, bukan HTML
    },
    body: JSON.stringify({
      user_id: currentUser.id,   // ← kirim snake_case sesuai validasi backend
      content: content,
    }),
  });

  if (!res.ok) {
    // status 422 → validasi gagal
    const data = await res.json();
    throw new Error(data.message ?? "Gagal membuat post");
  }

  const data = await res.json();

  // post dari server pakai user_id → terjemahkan ke userId (sama seperti di feed)
  const newPost = { ...data.post, userId: data.post.user_id };
  setPosts((prev) => [newPost, ...prev]);   // tampilkan post dari server (sudah punya id asli)
}
```

> 💡 **Beda penting:** `id` sekarang datang dari **database** (bukan `Date.now()`),
> dan post-nya **beneran tersimpan** — refresh halaman, post tetap ada. 💪

> 💡 **Soal nama field:** kamu mengirim `user_id` (snake_case) karena itu yang diminta
> validasi backend. Tapi waktu post-nya mau ditampilkan, kamu terjemahkan lagi ke
> `userId` seperti di feed. Ingat: **kirim pakai `user_id`, tampilkan pakai `userId`.** 😉

---

## 11. ❤️👎 React Consume API — Like & Dislike

### Cara LAMA (lokal)

```jsx
function handleLike(postId) {
  setPosts((posts) => posts.map((p) => p.id === postId ? { ...p, likes: p.likes + 1 } : p));
  // ... logika toggle reactions
}
```

### Cara BARU (ke API)

```jsx
async function handleLike(postId) {
  const res = await fetch(`${import.meta.env.VITE_API_URL}/api/posts/${postId}/like`, {
    method: "POST",
    headers: { "Accept": "application/json" },
  });
  const data = await res.json();

  // ganti post lama dengan versi terbaru dari server
  setPosts((posts) => posts.map((p) => (p.id === postId ? data.post : p)));
}
```

> 💡 Angka like sekarang **otoritasnya di server**. React ga nebak-nebak +1 sendiri;
> dia ambil nilai terbaru dari response (`data.post.likes`). Lebih akurat kalau banyak user.

> 🪜 **Catatan toggle:** state `reactions` (biar tombol nyala & bisa batal) tetap boleh
> dipegang React buat UX. Yang pindah ke server cuma **angka** like/dislike-nya.

---

## 12. 🔐 Soal Login — Kenapa Masih Lokal?

Di sesi ini **login tetap diproses lokal** di React (dicek ke array `users` di
`src/data/user.js`), **belum lewat API.** Jadi yang pindah ke database baru **post** dan
**like/dislike** — login belum.

**Kenapa belum sekarang?** Login lewat API itu topik yang cukup besar sendiri. Biar
jalan, kamu bakal butuh beberapa hal baru sekaligus:
- cara mengecek password dengan aman di server (bukan dibandingkan apa adanya),
- token login yang dikirim balik ke React tiap kali berhasil masuk,
- cara menyimpan token itu di browser dan mengirimnya di setiap request berikutnya.

Fokus kita sekarang adalah *data post yang benar-benar tersimpan di database* — dan itu
sudah tercapai tanpa login API. Jadi login kita biarkan apa adanya dulu, supaya tidak
menumpuk terlalu banyak hal baru dalam satu sesi.

```
Login        →  tetap lokal (array users di React)   ← nanti, di sesi lanjutan
Post         →  API → Database                         ← Sesi 6+7 ✅
Like/Dislike →  API → Database                         ← Sesi 6+7 ✅
```

> 🎁 Penasaran gimana login lewat API yang sebenarnya? Ada gambaran singkatnya di
> bagian **Bonus** paling bawah. Anggap itu bocoran buat sesi berikutnya — belum perlu
> kamu kerjakan sekarang. 😉

---

## 🧩 13. Apply to Threadly

### Struktur yang tersentuh

**Backend — `threadly-api/`**
```
threadly-api/
├── app/
│   └── Http/
│       ├── Controllers/
│       │   └── PostController.php     ← + store(), like(), dislike()
│       └── Middleware/
│           └── LogRequests.php        ← (opsional) demo middleware
├── bootstrap/
│   └── app.php                        ← daftar middleware api
├── config/
│   └── cors.php                       ← izinkan origin http://localhost:5173
└── routes/
    └── api.php                        ← + POST /posts, /posts/{id}/like, /dislike
```

> 💡 Perhatikan: di backend **tidak ada** perubahan di `Post.php` soal nama field.
> Backend tetap pakai `user_id`. Penyesuaian nama dilakukan di React (bagian 08).

**Frontend — `threadly-react/`**
```
threadly-react/
├── .env                               ← VITE_API_URL=http://localhost:8000
├── .env.example                       ← contoh buat tim (tanpa nilai rahasia)
└── src/
    └── App.jsx                        ← satu-satunya file React yang diubah:
                                          fetch feed + loading/error, normalisasi userId,
                                          handleNewPost / handleLike / handleDislike → API
```

> 💡 `HomePage.jsx`, `ProfilePage.jsx`, dan komponen lain **tidak disentuh** — berkat
> normalisasi `userId` di `App.jsx`, semuanya tetap bekerja seperti sebelumnya.

### 🔄 Alur Lengkap (INTI Sesi 6+7)

**Bikin post baru:**
```
User ketik di PostComposer (React)
        ↓  klik "Post"
fetch POST /api/posts  { user_id, content }     ← React kirim (Sesi 7)
        ↓  (lolos CORS)
[ Middleware ]  → log request                   ← Sesi 6
        ↓
PostController@store                            ← Sesi 6
        ↓  $request->validate(...)              ← validasi; gagal → 422
Post::create($validated)                        ← simpan ke DB
        ↓
response()->json(compact('post'), 201)          ← balikin post + id asli
        ↓
React: terjemahkan user_id → userId, lalu       ← tampil di feed, tersimpan permanen 💪
       setPosts([postBaru, ...prev])
```

---

## 🧠 14. Recap

### Konsep Inti

| Konsep | Inti |
|---|---|
| **Validasi** | `$request->validate([...])` — jangan percaya data client |
| **Status 201** | berhasil **membuat** data (beda dari 200) |
| **Status 422** | validasi **gagal** (Laravel balikin `errors`) |
| **`store`** | konvensi nama method buat `POST` (bikin data baru) |
| **Middleware** | pos pemeriksaan sebelum Controller (`$next` = lanjut) |
| **`increment()`** | Eloquent: tambah nilai kolom & langsung simpan |
| **CORS** | aturan browser; server harus izinkan origin frontend |
| **Normalisasi `userId`** | terjemahkan `user_id` (API) → `userId` (React) di `App.jsx` |
| **`import.meta.env.VITE_*`** | env var Vite — base URL API, bukan hardcode |
| **loading / error state** | React harus tahu lagi ambil data / ada gagal |

### Perintah penting

```bash
# Backend (Laravel)
php artisan make:middleware LogRequests     # bikin middleware
php artisan config:publish cors             # publish config CORS
php artisan serve                           # jalankan API (port 8000)

# Frontend (React)
npm run dev                                 # jalankan Vite (port 5173)
```

### 🧠 One-liner buat diingat

```
VALIDASI   → "data client udah bener belum sebelum disimpan?"
STORE      → "nerima POST, simpan ke DB, balikin 201"
MIDDLEWARE → "gerbang yang dilewati request sebelum masuk controller"
CORS       → "server ngizinin origin mana yang boleh manggil"
.env (Vite)→ "alamat API ga di-hardcode, gampang ganti pas deploy"
LOADING    → "kasih tau user lagi nunggu data"
```

### 🎯 Final Takeaway

> **"Kalau user bikin post di React, gimana sampai ke database dan balik lagi?"**

```
React (POST + body)
  → CORS lolos
  → Middleware
  → Controller@store
  → validate()
  → Post::create()
  → JSON 201 { post }
  → React tampilkan (data permanen)
```

**Selanjutnya (Sesi 8 — Deployment):** backend + frontend yang sekarang jalan di
`localhost` bakal di-deploy ke server beneran + web server **NGINX**. 🚀

---

## 🎁 BONUS — Good to Know

> Di luar materi inti. Ga wajib, buat nambah wawasan. 😎

### 🔁 Validasi pakai Form Request (lebih rapi)

Kalau validasi makin banyak, pindahkan dari controller ke class khusus:

```bash
php artisan make:request StorePostRequest
```

Lalu controller cukup: `public function store(StorePostRequest $request)` —
validasi jalan otomatis sebelum method dieksekusi.

### 🧷 API Resource (rapiin bentuk JSON)

```bash
php artisan make:resource PostResource
```

Dipakai buat ngatur persis field apa yang keluar di JSON (mis. langsung kirim `userId`
dari backend, sembunyikan `updated_at`). Kalau project membesar, ini cara yang lebih
rapi — dan backend bisa langsung ngasih `userId`, jadi React ga perlu normalisasi lagi.

### 🔐 Login lewat API beneran (Sanctum) — sekilas

Kalau nanti login mau pindah ke API, kira-kira begini alurnya:

```
POST /api/login { email, password }
  → cek user di tabel users
  → kalau cocok: buat token  ($user->createToken(...))
  → balikin token ke React
React simpan token → kirim di header tiap request:
  Authorization: Bearer <token>
Route yang butuh login dibungkus middleware auth:sanctum
```

Dengan token ini, endpoint seperti like/dislike bisa tahu **siapa** yang nge-like —
syarat buat fitur "1 user 1 reaksi" yang benar. Simpan rasa penasaranmu buat sesi
lanjutan ya. 😉

### 🌐 Status code yang relevan sesi ini

| Code | Arti |
|---|---|
| `200` | OK (sukses umum, mis. GET) |
| `201` | Created (berhasil POST bikin data) |
| `404` | Not Found (post id ga ada) |
| `422` | Unprocessable — validasi gagal |
| `401` | Unauthorized (belum login — kalau nanti pakai auth) |

### 🪣 Kenapa `Accept: application/json` penting di React

Kalau header ini ga dikirim, Laravel kadang balikin halaman **HTML error** (bukan JSON)
saat validasi gagal, dan `res.json()` di React jadi error. Dengan `Accept: application/json`,
Laravel dijamin balikin error dalam bentuk JSON yang rapi.
