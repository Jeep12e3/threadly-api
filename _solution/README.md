# 🔑 _solution — Kunci Jawaban

Folder ini **BUKAN bagian aplikasi** (tidak dipakai Laravel). Isinya versi **jadi** dari
tiap langkah, untuk referensi mentor / cek jawaban peserta.

> Panduan langkah: `../LANGKAH-SESI-5.md` (Sesi 5) dan `../LANGKAH-SESI-6-7.md` (Sesi 6+7).

---

## 📦 Base branch ini = AKHIR Sesi 5

Berbeda dengan `main` (yang berada di akhir Sesi 4), branch `sesi-6-7-middleware-fullstack`
**sudah mengandung hasil akhir Sesi 5** sebagai titik awal:

- `app/Models/Post.php` → Model `Post` (sudah ada)
- `database/migrations/*_create_posts_table.php` → tabel `posts` (sudah ada)
- `database/seeders/PostSeeder.php` + `DatabaseSeeder.php` → data awal (sudah ada)
- `app/Http/Controllers/PostController.php` → `index()` & `show()` baca DB (sudah ada)
- `routes/api.php` → `GET /posts` & `GET /posts/{id}` (sudah ada)
- `.env.example` → sudah `DB_CONNECTION=mysql`

Jadi peserta **langsung mulai dari Sesi 6**, tidak perlu mengulang Sesi 5.

---

## Kunci jawaban Sesi 5 (referensi base)

| Konsep | File jawaban |
|---|---|
| Migration `posts` | `create_posts_table.php` |
| Model `Post` | `Post.php` |
| Seeder | `PostSeeder.php`, `DatabaseSeeder.php` |
| Controller index (array → DB) | `PostController.step5.php` |
| Controller final (compact + show) | `PostController.php`, `routes-api.php` |

## Kunci jawaban Sesi 6 — Middleware & Logic

| Step | Yang dikerjakan peserta | File jawaban |
|---|---|---|
| 2 | Route `POST /posts` + method `store()` + validasi (201/422) | `routes-api.step6.php`, `PostController.step6.php` |
| 4 | Route + method `like()` & `dislike()` (`increment`, 404) | `routes-api.step6.php`, `PostController.step6.php` |
| 5 | (opsional) middleware logging | `LogRequests.php` |

## Kunci jawaban Sesi 7 — Full-Stack Integration

| Step | Yang dikerjakan peserta | File jawaban |
|---|---|---|
| 6 | CORS: izinkan origin Vite `:5173` | `cors.php` |
| 7 | `.env` Vite (base URL API) | `env-vite.txt` |
| 8–10 | React consume API (feed + loading/error + create + like/dislike) | `App.jsx` |

> ⚠️ `App.jsx` & `env-vite.txt` dipasang di repo **`threadly-react`**, bukan di sini.
> Diletakkan di folder ini hanya biar satu tempat semua kunci jawaban sesi.

## Perintah yang dijalankan peserta (urut, Sesi 6+7)

```bash
# --- Sesi 6 (backend) ---
# edit routes/api.php + PostController (store, like, dislike)
php artisan serve
# tes POST pakai Thunder Client / Postman:
#   POST http://localhost:8000/api/posts   body { "user_id": 2, "content": "..." }

# (opsional) middleware
php artisan make:middleware LogRequests     # lalu daftarkan di bootstrap/app.php

# --- Sesi 7 (CORS + React) ---
php artisan config:publish cors             # lalu edit config/cors.php (allowed_origins)

# di repo threadly-react:
#   bikin .env  → VITE_API_URL=http://localhost:8000
npm run dev                                 # Vite :5173
```
