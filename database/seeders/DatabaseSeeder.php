<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Uygulamanın veritabanını tohumlar.
     */
    public function run(): void
    {
        // Seeder'ları doğru sırayla çağır
        $this->call([
            // 1. ROLLER VE İZİNLER (En başta olmalı)
            RolesSeeder::class,            // Mevcut Roller
            DisciplinaryRoleSeeder::class, // YENİ: Disiplin Rolleri ve İzinleri

            // 2. TEMEL VERİLER
            BolumSeeder::class,            // Bölümler
            DisciplinarySeeder::class,     // YENİ: Disiplin Kategorileri, Cezalar vb.

            // 3. YETKİLENDİRME VE KULLANICILAR
            SikayetYetkiSeeder::class,     // Şikayet izinleri
            UserSeeder::class,             // Kullanıcılar (Roller artık var olduğu için hata vermez)
        ]);
    }
}