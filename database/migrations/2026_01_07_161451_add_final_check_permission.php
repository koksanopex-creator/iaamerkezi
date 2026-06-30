<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Yetkiyi Oluştur
        $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'arabuluculuk.final_check', 'guard_name' => 'web']);
        
        // 2. Varsayılan Olarak Hukuk Admini ve Superadmin'e Ver
        $roles = ['Hukuk Admini', 'Superadmin'];
        foreach($roles as $roleName) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            if($role) $role->givePermissionTo($perm);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
