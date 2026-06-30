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
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            // Check if oylama_notu exists. If not, add it.
            if (!Schema::hasColumn('disciplinary_cases', 'oylama_notu')) {
                if (Schema::hasColumn('disciplinary_cases', 'oylama_aktif')) {
                    $table->text('oylama_notu')->nullable()->after('oylama_aktif');
                } else {
                    $table->text('oylama_notu')->nullable();
                }
            }

            if (!Schema::hasColumn('disciplinary_cases', 'oylama_baslatan_id')) {
                if (Schema::hasColumn('disciplinary_cases', 'oylama_notu')) {
                    $table->foreignId('oylama_baslatan_id')->nullable()->constrained('users')->after('oylama_notu');
                } else {
                    $table->foreignId('oylama_baslatan_id')->nullable()->constrained('users');
                }
            }

            if (!Schema::hasColumn('disciplinary_cases', 'oylama_baslatildi_at')) {
                $table->timestamp('oylama_baslatildi_at')->nullable()->after('oylama_baslatan_id');
            }

            if (!Schema::hasColumn('disciplinary_cases', 'oylama_bitti_at')) {
                $table->timestamp('oylama_bitti_at')->nullable()->after('oylama_baslatildi_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropForeign(['oylama_baslatan_id']);
            $table->dropColumn(['oylama_baslatan_id', 'oylama_baslatildi_at', 'oylama_bitti_at']);
        });
    }
};
