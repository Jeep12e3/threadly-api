# 🔌 Threadly API (Laravel)

Backend untuk project **Threadly** — dibangun di sesi training:

- **Sesi 4 — API Fundamentals:** MVC, Routing & Controller, JSON Output
- **Sesi 5 — Database Integration:** Migration, Model (the "M"), `compact()` output

Frontend-nya ada di repo terpisah: **`threadly-react`**. Keduanya baru disambungkan di **Sesi 7**.

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

### 3. Pilih database

**Opsi A — SQLite (default, langsung jalan):**
Tidak perlu setup apa-apa. `.env` sudah `DB_CONNECTION=sqlite`.
```bash
php artisan migrate --seed
```

**Opsi B — MySQL (XAMPP), sesuai materi Sesi 5:**
1. Nyalakan **MySQL** di XAMPP Control Panel.
2. Buka phpMyAdmin → buat database bernama **`threadly`**.
3. Di `.env`: comment `DB_CONNECTION=sqlite`, lalu aktifkan blok MySQL (lihat `.env.example`).
4. Jalankan:
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

Tes cepat (browser atau terminal):
```bash
curl http://localhost:8000/api/posts
```

---

## 🗂️ Struktur Penting

```
app/
├── Models/
│   └── Post.php                     # Model (mewakili tabel posts)
└── Http/Controllers/
    └── PostController.php           # index() & show()
database/
├── migrations/
│   └── *_create_posts_table.php     # cetak biru tabel posts
└── seeders/
    ├── PostSeeder.php               # data awal (= posts.js di React)
    └── DatabaseSeeder.php           # memanggil PostSeeder
routes/
└── api.php                          # /posts & /posts/{id}
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

## 🛣️ Rencana Sesi Berikutnya

- **Sesi 6 — Middleware & Logic:** tambah endpoint `POST /api/posts` + **validasi** data & middleware.
- **Sesi 7 — Full-Stack Integration:** konfigurasi **CORS**, lalu `threadly-react` consume API ini (ganti DummyJSON → API sendiri).
- **Sesi 8 — Deployment:** deploy backend + web server NGINX.
