<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Dipanggil saat `php artisan db:seed` atau `php artisan migrate --seed`.
     */
    public function run(): void
    {
        $this->call(PostSeeder::class);
    }
}
