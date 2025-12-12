<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DisciplinaryRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. İzinleri Oluştur
        $permissions = [
            'disiplin.goruntule',       // Dosyaları görme
            'disiplin.yonet',           // Süreci ilerletme
            'disiplin.ayarlar',         // Ayar sayfasına giriş
            'disiplin.raporla',         // Yeni disiplin suçu bildirme
            'disiplin.oy_ver',          // Kurul üyesi oy kullanma
            'disiplin.son_karar',       // Kurul başkanı karar verme
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Rolleri Oluştur ve İzinleri Ata

        // A. HUKUK ADMİNİ (Sistemi Kuran)
        $roleAdmin = Role::firstOrCreate(['name' => 'Hukuk Admini']);
        $roleAdmin->syncPermissions([
            'disiplin.goruntule',
            'disiplin.yonet',
            'disiplin.ayarlar', 
            'disiplin.raporla'
        ]);

        // B. HUKUK YÖNETİCİSİ (Süreci İşleten)
        $roleManager = Role::firstOrCreate(['name' => 'Hukuk Yöneticisi']);
        $roleManager->syncPermissions([
            'disiplin.goruntule',
            'disiplin.yonet',
            'disiplin.raporla'
        ]);

        // C. DİSİPLİN KURULU BAŞKANI
        $roleHead = Role::firstOrCreate(['name' => 'Disiplin Kurulu Başkanı']);
        $roleHead->syncPermissions(['disiplin.goruntule', 'disiplin.oy_ver', 'disiplin.son_karar']);

        // D. DİSİPLİN KURULU ÜYESİ
        $roleMember = Role::firstOrCreate(['name' => 'Disiplin Kurulu Üyesi']);
        $roleMember->syncPermissions(['disiplin.goruntule', 'disiplin.oy_ver']);
    }
}