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
    Schema::table('arabuluculuk_payments', function (Blueprint $table) {
        // Eğer bu sütunlar yoksa ekle
        if (!Schema::hasColumn('arabuluculuk_payments', 'banka_adi')) {
            $table->string('banka_adi')->nullable()->after('odenecek_kisi');
            $table->string('iban', 26)->nullable()->after('banka_adi');
            $table->date('son_odeme_tarihi')->nullable()->after('tutar');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arabuluculuk_payments', function (Blueprint $table) {
            //
        });
    }
};
