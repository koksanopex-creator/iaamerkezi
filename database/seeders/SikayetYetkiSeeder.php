<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SikayetYetkiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. "Kurul" rolünü oluştur (eğer zaten yoksa)
        $kurulRole = Role::firstOrCreate(['name' => 'Müşteri Şikayeti Kurulu']);
        
        // 2. "şikayet olustur" iznini oluştur
        $permission = Permission::firstOrCreate(['name' => 'sikayet olustur']);

        // 3. Bu izni hem Superadmin hem de Kurul rollerine ata
        $superadminRole = Role::findByName('Superadmin');
        $superadminRole->givePermissionTo($permission);
        $kurulRole->givePermissionTo($permission);
    }
}