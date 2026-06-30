<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HukukYetkiPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsConfig = config('hukuk_permissions');

        if (!$permissionsConfig) {
            $this->command->error("config/hukuk_permissions.php bulunamadı veya boş!");
            return;
        }

        $allPermissions = [];
        foreach ($permissionsConfig as $group => $perms) {
            foreach ($perms as $slug => $label) {
                // Ensure permission exists in DB
                Permission::firstOrCreate(
                    ['name' => $slug, 'guard_name' => 'web']
                );
                $allPermissions[] = $slug;
            }
        }

        // Opsiyonel olarak, "Hukuk Admini" ve "Superadmin" rollerine 
        // bu yetkilerin hepsini atayabilirsiniz:
        $hukukAdminRole = Role::where('name', 'Hukuk Admini')->first();
        if ($hukukAdminRole) {
            $hukukAdminRole->givePermissionTo($allPermissions);
        }

        $superAdminRole = Role::where('name', 'Superadmin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
        }

        $this->command->info("Hukuk yetki matrisi izinleri başarıyla eklendi.");
    }
}
