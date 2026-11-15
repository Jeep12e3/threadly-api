<?php
// ✅ KUNCI JAWABAN Sesi 7 — config/cors.php
// Dihasilkan lewat: php artisan config:publish cors
// Lalu edit allowed_origins supaya dev server React (Vite :5173) boleh manggil API.

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',   // ← dev server Vite (React)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
