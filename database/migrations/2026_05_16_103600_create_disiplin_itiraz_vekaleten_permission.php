<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Bölüm Lider Yardımcısı'na verilebilecek vekâleten itiraz yetkisi
        Permission::firstOrCreate(['name' => 'disiplin.itiraz.vekaleten', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        Permission::where('name', 'disiplin.itiraz.vekaleten')->delete();
    }
};
