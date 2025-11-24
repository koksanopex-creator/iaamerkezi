<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Gerekirse Permission da ekleyebilirsiniz
// === YENİ ===
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Bu satır eklendi

class RolesSeeder extends Seeder // Class adı RolesSeeder olarak değiştirildi
{
    use WithoutModelEvents; // Bu satır eklendi

    /**
     * Veritabanını temel rollerle tohumlar.
     */
    public function run(): void
    {
        // Temiz başlangıç için mevcut rolleri silmek isterseniz yorumu kaldırın:
        // Role::query()->delete(); 
        // Dikkat: Bu, mevcut tüm rolleri ve ilişkili izinleri siler!

        // === DEĞİŞTİ: Tümü firstOrCreate ile değiştirildi ===
        Role::firstOrCreate(['name' => 'Superadmin']);
        Role::firstOrCreate(['name' => 'Bölüm Lideri']);
        Role::firstOrCreate(['name' => 'Kullanıcı']);
        Role::firstOrCreate(['name' => 'Müşteri Şikayeti Kurulu']);
        Role::firstOrCreate(['name' => 'Müşteri Şikayeti Çözüm Lideri']);
        Role::firstOrCreate(['name' => 'Bölüm Kalite Yöneticisi']);
        // ===============================================

        // İzinler eklemek isterseniz örnek:
        // Permission::firstOrCreate(['name' => 'şikayetleri çöz']);
        // $cozumLideriRole = Role::findByName('Müşteri Şikayeti Çözüm Lideri');
        // $cozumLideriRole?->givePermissionTo('şikayetleri çöz'); // ?-> null kontrolü ekler
    }
}

