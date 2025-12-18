<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ENDİŞENİ ÇÖZEN KOLON:
            // true: Bizim personelimiz (Mevcutlar bozulmasın diye default true)
            // false: Müşteri temsilcisi
            $table->boolean('is_personnel')->default(true)->after('id');

            // Eğer müşteri temsilcisi ise, hangi firmaya bağlı?
            $table->foreignId('customer_id')->nullable()->after('bolum_id')
                  ->constrained('customers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['is_personnel', 'customer_id']);
        });
    }
};