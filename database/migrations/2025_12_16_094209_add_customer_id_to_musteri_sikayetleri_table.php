<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Şikayetin hangi "Firma"dan geldiğini tutmak için
            $table->foreignId('customer_id')->nullable()->after('musteri_adi')
                  ->constrained('customers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};