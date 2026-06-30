<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ArabuluculukRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GRANÜLER (DETAYLI) İZİNLERİ TANIMLA
        // Bu izinler Admin Panelindeki tabloda görünecek.
        $permissions = [
            'arabuluculuk.view_menu',          // Sol menüde başlığı görme
            'arabuluculuk.create_ihtiyari',    // İhtiyari dosya açabilme (Personel)
            'arabuluculuk.create_zorunlu',     // Zorunlu dosya açabilme (Hukuk)
            'arabuluculuk.view_zorunlu_files', // Zorunlu dosyaları listede görme
            'arabuluculuk.view_all_files',     // Tüm arşiv dosyalarını görme
            'arabuluculuk.view_assigned',      // Sadece kendine atananı görme (Dış Avukat)
            
            'arabuluculuk.upload_file',        // Dosya Yükleme
            'arabuluculuk.approve_legal',      // Hukuk Onayı
            'arabuluculuk.approve_board',      // Yönetim Onayı
            'arabuluculuk.finance_pay',        // Finans Ödeme
            'arabuluculuk.manage_payee',       // Alacaklı Tanımla (Hukuk)
            'arabuluculuk.board_vote',         // Oy Kullanma
            
            // Eski kodlarla uyumluluk için bunları da tutuyoruz:
            'arabuluculuk.create',
            'arabuluculuk.edit',
            'arabuluculuk.view_all',

            // --- YENİ EKLENECEK TANIMLAMA İZİNLERİ ---
            'arabuluculuk.settings_view',   // Sayfayı Görüntüleme
            'arabuluculuk.settings_create', // Ekleme Yapma
            'arabuluculuk.settings_delete', // Silme Yapma
            'arabuluculuk.settings_edit',   // Madde Düzenleme
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. ROLLERİ TANIMLA VE VARSAYILAN İZİNLERİ ATA

        // A) PERSONEL BİRİMİ (Sadece İhtiyari)
        // Servet Bey ve Ekibi: Zorunlu yetkileri YOK.
        $personelRoles = ['Arabuluculuk Personel Lideri', 'Arabuluculuk Personel'];
        foreach($personelRoles as $rName) {
            $role = Role::firstOrCreate(['name' => $rName]);
            // Mevcut izinleri temizleyip yenilerini verelim (sync)
            $role->syncPermissions([
                'arabuluculuk.view_menu',
                'arabuluculuk.create_ihtiyari', // Sadece İhtiyari
                'arabuluculuk.view_all_files',  // Hepsini görsün ama Controller'da zorunluları filtreleyeceğiz
                'arabuluculuk.upload_file',
                'arabuluculuk.create',
                'arabuluculuk.edit'
            ]);
        }

        // B) HUKUK BİRİMİ (Hem İhtiyari Hem Zorunlu)
        $hukukRoles = ['Hukuk Admini', 'Hukuk Yöneticisi'];
        foreach($hukukRoles as $rName) {
            $role = Role::firstOrCreate(['name' => $rName]);
            // Hukukçulara (neredeyse) tüm arabuluculuk yetkilerini ver
            $role->givePermissionTo(Permission::where('name', 'like', 'arabuluculuk.%')->get());
        }

        // C) KURUL (Başkan ve Üye)
        $kBaskan = Role::firstOrCreate(['name' => 'Arabuluculuk Kurulu Başkanı']);
        $kBaskan->syncPermissions(['arabuluculuk.view_menu', 'arabuluculuk.view_all_files', 'arabuluculuk.board_vote', 'arabuluculuk.approve_board']);

        $kUye = Role::firstOrCreate(['name' => 'Arabuluculuk Kurulu Üyesi']);
        $kUye->syncPermissions(['arabuluculuk.view_menu', 'arabuluculuk.view_all_files', 'arabuluculuk.board_vote']);

        // D) FİNANS
        $finans = Role::firstOrCreate(['name' => 'Arabuluculuk Finans']);
        $finans->syncPermissions(['arabuluculuk.view_menu', 'arabuluculuk.finance_pay', 'arabuluculuk.upload_file', 'arabuluculuk.view_all_files']);

        // E) DIŞ AVUKAT
        $disAvukat = Role::firstOrCreate(['name' => 'Dış Avukat']);
        $disAvukat->syncPermissions(['arabuluculuk.view_menu', 'arabuluculuk.view_assigned', 'arabuluculuk.upload_file']);

        // F) ÜST YÖNETİM
        $yonetim = Role::firstOrCreate(['name' => 'Yonetim']); 
        if($yonetim) {
             $yonetim->givePermissionTo(['arabuluculuk.view_menu', 'arabuluculuk.view_all_files', 'arabuluculuk.approve_board', 'arabuluculuk.create_zorunlu', 'arabuluculuk.view_zorunlu_files']);
        }
    }
}