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
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->json('active_widgets')->nullable()->after('durum');
            $table->text('toplanti_karari')->nullable()->after('active_widgets');
            $table->dateTime('baslatilma_at')->nullable()->change(); // Ensure nullability if not already
        });

        Schema::table('disiplin_kurulu_toplanti_katilimci', function (Blueprint $table) {
            $table->boolean('katildi_mi')->default(false)->after('katilim_durumu');
        });
    }

    public function down(): void
    {
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->dropColumn(['active_widgets', 'toplanti_karari']);
        });

        Schema::table('disiplin_kurulu_toplanti_katilimci', function (Blueprint $table) {
            $table->dropColumn('katildi_mi');
        });
    }
};
