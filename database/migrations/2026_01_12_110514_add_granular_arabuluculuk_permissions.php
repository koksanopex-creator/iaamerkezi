<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Yeni Granüler Yetkiler
        $perms = [
            'arabuluculuk.tab_genel_view',   // Genel Bakış sekmesini görme
            'arabuluculuk.tab_kurul_view',   // Kurul sekmesini görme
            'arabuluculuk.tab_log_view',     // Geçmiş/Log sekmesini görme
            'arabuluculuk.upload_all_files', // Her türlü dosyayı yükleme (Yoksa sadece dekont)
        ];

        foreach ($perms as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 2. Varsayılan Olarak Mevcut Rollere Ata (Kolaylık olsun diye)
        // Hukuk ve Superadmin hepsini görsün
        $fullAccessRoles = Role::whereIn('name', ['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi'])->get();
        foreach($fullAccessRoles as $role) {
            $role->givePermissionTo($perms);
        }

        // Personel: Genel ve Log görsün, ama Kurul görmesin
        $personel = Role::where('name', 'Arabuluculuk Personel')->first();
        if($personel) {
            $personel->givePermissionTo(['arabuluculuk.tab_genel_view', 'arabuluculuk.tab_log_view', 'arabuluculuk.upload_all_files']);
        }
        
        // FİNANS: HİÇBİR TAB YETKİSİ VERMİYORUZ (Sadece Ödeme sekmesi yetkisiz açık kalacak)
        // Finans'ın sadece dekont yüklemesi için 'upload_all_files' yetkisini de VERMİYORUZ.
    }

    public function down(): void
    {
        // Geri alma işlemleri...
    }
};