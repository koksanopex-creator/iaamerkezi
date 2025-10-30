<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// === YENİ ===
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents; // Eklendi

    /**
     * Uygulamanın veritabanını tohumlar.
     */
    public function run(): void
    {
        // Seeder'ları doğru sırayla çağır
        $this->call([
            RolesSeeder::class,          // Önce Roller (yeni rolü içeriyor)
            BolumSeeder::class,          // Sonra Bölümler
            SikayetYetkiSeeder::class,   // === YENİ: Şikayet izinlerini ata ===
            UserSeeder::class,           // En son Kullanıcılar (rolleri atayacak)
        ]);
    }
}
