<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Veritabanını temel roller ve dinamik yetkilerle tohumlar.
     */
    public function run(): void
    {
        // 1. TEMEL ROLLERİN OLUŞTURULMASI
        $roles = [
            'Superadmin',
            'Yonetim',
            'Bölüm Lideri',
            'Bölüm Lider Yardımcısı', // Yeni rol
            'Kullanıcı',
            'Müşteri Şikayeti Kurulu',
            'Müşteri Şikayeti Çözüm Lideri',
            'Müşteri Temsilcisi',
            'Bölüm Kalite Yöneticisi',
            'Direktör',
            'Hukuk Admini',
            'Hukuk Yöneticisi',
            'Arabuluculuk Personel',
            'Disiplin Kurulu Başkanı',
            'Disiplin Kurulu Üyesi',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. DİNAMİK YETKİLERİN (PERMISSIONS) OLUŞTURULMASI
        // config/bolum_permissions.php dosyasından yetkileri çekiyoruz
        $bolumPermissions = config('bolum_permissions');

        if ($bolumPermissions) {
            foreach ($bolumPermissions as $category => $permissions) {
                foreach ($permissions as $permissionKey => $description) {
                    Permission::firstOrCreate([
                        'name' => $permissionKey,
                        'guard_name' => 'web'
                    ]);
                }
            }
        }

        // 3. ROLLER VE YETKİLERİN EŞLEŞTİRİLMESİ (OPSİYONEL - TEMEL ATAMALAR)
        
        // Yönetim ve Direktör gibi rollere genel görüntüleme yetkileri verilebilir
        $yonetimRole = Role::where('name', 'Yonetim')->first();
        if ($yonetimRole) {
            $yonetimPerms = [
                'bolum.sikayet.gor',
                'bolum.iaa.gor',
                'bolum.dashboard.ozet'
            ];

            // Atama yapmadan önce yetkilerin varlığından emin ol (Hata önleyici)
            foreach ($yonetimPerms as $permName) {
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }

            $yonetimRole->syncPermissions($yonetimPerms);
        }

        $this->command->info('Roller ve yetkiler başarıyla senkronize edildi.');
    }
}
