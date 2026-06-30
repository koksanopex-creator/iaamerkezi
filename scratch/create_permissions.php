<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$permissions = [
    'disiplin.ayarlar.gor', 'disiplin.ayarlar.duzenle',
    'arabuluculuk.tanimlar.gor', 'arabuluculuk.tanimlar.duzenle',
    'dis-avukatlar.gor', 'dis-avukatlar.duzenle',
    'disiplin.portal.gor', 'disiplin.tutanak.olustur', 'disiplin.tutanak.duzenle',
    'disiplin.degerlendirme.gor', 'disiplin.degerlendirme.kullan',
    'disiplin.personel.savunma.yaz',
    'disiplin.kurul.portal.gor', 'disiplin.kurul.uye.yonet', 'disiplin.kurul.toplanti.yonet', 'disiplin.kurul.toplanti.aktif_et'
];

foreach ($permissions as $p) {
    Permission::firstOrCreate(['name' => $p]);
}

// Superadmin'e otomatik verilecek
$super = Role::firstOrCreate(['name' => 'Superadmin']);
$super->givePermissionTo(Permission::all());

// Hukuk Admini'ne de varsayılan olarak verelim ki matrisi yönetebilsin
$hukukAdmin = Role::firstOrCreate(['name' => 'Hukuk Admini']);
$hukukAdmin->givePermissionTo(Permission::all());

echo "Success: Permissions created and assigned to roles.\n";
