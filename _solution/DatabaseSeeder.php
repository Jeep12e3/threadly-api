<?php
// ✅ KUNCI JAWABAN Step 4 — database/seeders/DatabaseSeeder.php
// (daftarkan PostSeeder supaya ikut jalan saat `php artisan db:seed`)

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(PostSeeder::class);
    }
}
