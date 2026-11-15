<?php
// ✅ KUNCI JAWABAN Sesi 6 (OPSIONAL) — app/Http/Middleware/LogRequests.php
// Dibuat lewat: php artisan make:middleware LogRequests
// Demo konsep middleware: catat tiap request API sebelum masuk controller.
//
// Daftarkan di bootstrap/app.php:
//
//   ->withMiddleware(function (Middleware $middleware): void {
//       $middleware->api(append: [
//           \App\Http\Middleware\LogRequests::class,
//       ]);
//   })

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        // dijalankan SEBELUM controller
        Log::info('API dipanggil: ' . $request->method() . ' ' . $request->path());

        return $next($request);   // teruskan ke controller
    }
}
