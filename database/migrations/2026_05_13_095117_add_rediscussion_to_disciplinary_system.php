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
        // 1. First, make sure oylama_notu exists.
        if (!Schema::hasColumn('disciplinary_cases', 'oylama_notu')) {
            Schema::table('disciplinary_cases', function (Blueprint $table) {
                if (Schema::hasColumn('disciplinary_cases', 'oylama_aktif')) {
                    $table->text('oylama_notu')->nullable()->after('oylama_aktif');
                } else {
                    $table->text('oylama_notu')->nullable();
                }
            });
        }
        
        // 2. Next, make sure rediscussion_count exists.
        if (!Schema::hasColumn('disciplinary_cases', 'rediscussion_count')) {
            Schema::table('disciplinary_cases', function (Blueprint $table) {
                if (Schema::hasColumn('disciplinary_cases', 'oylama_notu')) {
                    $table->integer('rediscussion_count')->default(0)->after('oylama_notu');
                } else {
                    $table->integer('rediscussion_count')->default(0);
                }
            });
        }

        // 3. Next, make sure rediscussion_reason exists.
        if (!Schema::hasColumn('disciplinary_cases', 'rediscussion_reason')) {
            Schema::table('disciplinary_cases', function (Blueprint $table) {
                if (Schema::hasColumn('disciplinary_cases', 'rediscussion_count')) {
                    $table->text('rediscussion_reason')->nullable()->after('rediscussion_count');
                } else {
                    $table->text('rediscussion_reason')->nullable();
                }
            });
        }

        // 4. Update votes table.
        if (!Schema::hasColumn('disciplinary_votes', 'round')) {
            Schema::table('disciplinary_votes', function (Blueprint $table) {
                $table->integer('round')->default(1)->after('yorum');
            });
        }
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
