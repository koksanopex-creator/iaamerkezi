<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Yeni yetkiyi oluşturuyoruz
        // Bu isim 'arabuluculuk.' ile başladığı için SistemAyarController'daki
        // kodunuz bunu otomatik olarak yakalayıp o tabloya getirecek.
        Permission::firstOrCreate(['name' => 'arabuluculuk.assign_mediator', 'guard_name' => 'web']);

        // (Opsiyonel) Başlangıçta bu yetkiyi Superadmin'e ve Hukuk Admin'e verelim
        $roles = Role::whereIn('name', ['Superadmin', 'Hukuk Admini', 'Arabuluculuk Personel'])->get();
        foreach ($roles as $role) {
            $role->givePermissionTo('arabuluculuk.assign_mediator');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'arabuluculuk.assign_mediator')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};