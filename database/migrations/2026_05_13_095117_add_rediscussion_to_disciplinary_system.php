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
            
            // Check if rediscussion_count exists. If not, add it.
            if (!Schema::hasColumn('disciplinary_cases', 'rediscussion_count')) {
                if (Schema::hasColumn('disciplinary_cases', 'oylama_notu')) {
                    $table->integer('rediscussion_count')->default(0)->after('oylama_notu');
                } else {
                    $table->integer('rediscussion_count')->default(0);
                }
            }

            // Check if rediscussion_reason exists. If not, add it.
            if (!Schema::hasColumn('disciplinary_cases', 'rediscussion_reason')) {
                $table->text('rediscussion_reason')->nullable()->after('rediscussion_count');
            }
        });

        Schema::table('disciplinary_votes', function (Blueprint $table) {
            if (!Schema::hasColumn('disciplinary_votes', 'round')) {
                $table->integer('round')->default(1)->after('yorum');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropColumn(['rediscussion_count', 'rediscussion_reason']);
        });

        Schema::table('disciplinary_votes', function (Blueprint $table) {
            $table->dropColumn('round');
        });
    }
};
