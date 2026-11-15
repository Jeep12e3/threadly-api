# 🔌 Threadly API (Laravel)

Backend untuk project **Threadly** — dibangun di sesi training.

> 📌 **Branch `sesi-6-7-middleware-fullstack`.** Titik awalnya = **hasil akhir Sesi 5**
> (database sudah jalan: Model `Post`, migration, seeder, `GET /posts` & `/posts/{id}`).
> Fokus branch ini:
> - **Sesi 6 — Middleware & Logic:** `POST /api/posts` + validasi + like/dislike + middleware
> - **Sesi 7 — Full-Stack Integration:** CORS + `threadly-react` consume API ini
>
> Panduan langkah: **`LANGKAH-SESI-6-7.md`** · Konsep: **`SESI-6-7-MIDDLEWARE-FULLSTACK.md`**

Frontend-nya ada di repo terpisah: **`threadly-react`** (disambungkan di Sesi 7).

---

## 🚀 Cara Menjalankan

### 1. Install dependencies
```bash
composer install
```

### 2. Siapkan `.env`
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Siapkan database (MySQL / XAMPP)

`.env.example` sudah di-set ke **MySQL**.
1. Nyalakan **MySQL** di XAMPP Control Panel.
2. Buka phpMyAdmin → buat database bernama **`threadly`**.
3. Jalankan:
```bash
php artisan migrate --seed
```

### 4. Jalankan server
```bash
php artisan serve
```
Server jalan di `http://localhost:8000`.

---

## 📡 Endpoint

| Method | URL | Keterangan | Response |
|---|---|---|---|
| GET | `/api/posts` | semua post | `{ "posts": [...] }` |
| GET | `/api/posts/{id}` | satu post | `{ "post": {...} }` atau 404 |
| POST | `/api/posts` | bikin post baru (Sesi 6) | `{ "post": {...} }` 201 / error 422 |
| POST | `/api/posts/{id}/like` | tambah like (Sesi 6) | `{ "post": {...} }` |
| POST | `/api/posts/{id}/dislike` | tambah dislike (Sesi 6) | `{ "post": {...} }` |

Tes cepat GET (browser atau terminal):
```bash
curl http://localhost:8000/api/posts
```
> `POST` ga bisa lewat browser — pakai Thunder Client / Postman.

> 📌 **Branch ini (`sesi-6-7-middleware-fullstack`)** sudah berisi hasil akhir Sesi 5
> sebagai base. Endpoint `store`/`like`/`dislike` di atas dikerjakan di Sesi 6.
> Panduan: `LANGKAH-SESI-6-7.md` · Konsep: `SESI-6-7-MIDDLEWARE-FULLSTACK.md`.

---

## 🗂️ Struktur Penting

```
app/
├── Models/
│   └── Post.php                     # Model (mewakili tabel posts)
└── Http/Controllers/
    └── PostController.php           # index(), show() — + store()/like()/dislike() (Sesi 6)
database/
├── migrations/
│   └── *_create_posts_table.php     # cetak biru tabel posts
└── seeders/
    ├── PostSeeder.php               # data awal (= posts.js di React)
    └── DatabaseSeeder.php           # memanggil PostSeeder
routes/
└── api.php                          # /posts, /posts/{id} — + endpoint Sesi 6
_solution/                           # kunci jawaban Sesi 6+7 (bukan bagian aplikasi)
LANGKAH-SESI-6-7.md                  # panduan langkah praktik
SESI-6-7-MIDDLEWARE-FULLSTACK.md     # materi konsep lengkap
```

---

## 🧰 Perintah Artisan yang Sering Dipakai

```bash
php artisan serve                      # jalankan server dev
php artisan migrate                    # jalankan migration
php artisan migrate:fresh --seed       # reset DB + migrate + seed ulang
php artisan db:seed --class=PostSeeder # jalankan seeder saja
php artisan tinker                     # main-main dengan Eloquent di terminal
php artisan route:list                 # lihat semua route
```

---

## 🛣️ Sesi Berikutnya

- **Sesi 8 — Deployment:** deploy backend + frontend + web server NGINX.
