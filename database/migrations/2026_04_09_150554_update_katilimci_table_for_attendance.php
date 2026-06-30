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
        Schema::table('disiplin_kurulu_toplanti_katilimci', function (Blueprint $table) {
            if (!Schema::hasColumn('disiplin_kurulu_toplanti_katilimci', 'katilim_durumu')) {
                $table->enum('katilim_durumu', ['beklemede', 'katıldı', 'katılmadı'])->default('beklemede')->after('katildi_mi');
            }
            if (!Schema::hasColumn('disiplin_kurulu_toplanti_katilimci', 'katilmama_nedeni')) {
                $table->text('katilmama_nedeni')->nullable()->after('katilim_durumu');
            }
            if (Schema::hasColumn('disiplin_kurulu_toplanti_katilimci', 'katildi_mi')) {
                $table->dropColumn('katildi_mi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('disiplin_kurulu_toplanti_katilimci', function (Blueprint $table) {
            $table->boolean('katildi_mi')->default(false)->after('id');
            $table->dropColumn(['katilim_durumu', 'katilmama_nedeni']);
        });
    }
};
