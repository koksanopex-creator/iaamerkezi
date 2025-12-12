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
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Eğer sütunlar yoksa ekle (Hata almamak için kontrol ediyoruz)
            if (!Schema::hasColumn('musteri_sikayetleri', 'musteri_bildirim_yapan_id')) {
                $table->unsignedBigInteger('musteri_bildirim_yapan_id')->nullable()->after('guest_password_hash');
            }
            if (!Schema::hasColumn('musteri_sikayetleri', 'musteri_bildirim_tarihi')) {
                $table->timestamp('musteri_bildirim_tarihi')->nullable()->after('musteri_bildirim_yapan_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            //
        });
    }
};
