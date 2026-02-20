<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            if (!Schema::hasColumn('musteri_sikayetleri', 'lot_no')) {
                $table->string('lot_no')->nullable()->after('musteri_urun_veya_hizmet');
            }
            if (!Schema::hasColumn('musteri_sikayetleri', 'machine_id')) {
                $table->foreignId('machine_id')->nullable()->after('lot_no')->constrained('machines')->onDelete('set null');
            }
            if (!Schema::hasColumn('musteri_sikayetleri', 'genel_hammadde_id')) {
                // Before adding foreign key, ensure column doesn't exist.
                // Note: 'after' positioning relies on previous columns existing.
                $table->foreignId('genel_hammadde_id')->nullable()->after('machine_id')->constrained('genel_hammaddeler')->onDelete('set null');
            }
            if (!Schema::hasColumn('musteri_sikayetleri', 'urun_versiyonu_id')) {
                $table->foreignId('urun_versiyonu_id')->nullable()->after('genel_hammadde_id')->constrained('urun_versiyonlari')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->dropForeign(['machine_id']);
            $table->dropForeign(['genel_hammadde_id']);
            $table->dropForeign(['urun_versiyonu_id']);
            $table->dropColumn(['lot_no', 'machine_id', 'genel_hammadde_id', 'urun_versiyonu_id']);
        });
    }
};
