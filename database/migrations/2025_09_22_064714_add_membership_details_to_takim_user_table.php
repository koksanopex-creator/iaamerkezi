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
        Schema::table('takim_user', function (Blueprint $table) {

            // Sadece 'katilma_sekli' sütunu yoksa ekle.
            if (!Schema::hasColumn('takim_user', 'katilma_sekli')) {
                $table->string('katilma_sekli')->nullable();
            }

            // Sadece 'onay_durumu' sütunu yoksa ekle.
            if (!Schema::hasColumn('takim_user', 'onay_durumu')) {
                $table->string('onay_durumu')->default('onaylandi');
            }

            // Sadece 'onaylayan_user_id' sütunu yoksa ekle.
            if (!Schema::hasColumn('takim_user', 'onaylayan_user_id')) {
                $table->foreignId('onaylayan_user_id')->nullable()->constrained('users')->onDelete('set null');
            }

            // Sadece 'onay_tarihi' sütunu yoksa ekle.
            if (!Schema::hasColumn('takim_user', 'onay_tarihi')) {
                $table->timestamp('onay_tarihi')->nullable();
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('takim_user', function (Blueprint $table) {
            //
        });
    }
};
