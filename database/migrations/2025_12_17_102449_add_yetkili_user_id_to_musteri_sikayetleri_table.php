<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Eğer customer_id de yoksa onu da ekleyelim garanti olsun
            if (!Schema::hasColumn('musteri_sikayetleri', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('id');
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            }

            // Eksik olan yetkili_user_id sütunu
            if (!Schema::hasColumn('musteri_sikayetleri', 'yetkili_user_id')) {
                $table->unsignedBigInteger('yetkili_user_id')->nullable()->after('customer_id');
                $table->foreign('yetkili_user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->dropForeign(['yetkili_user_id']);
            $table->dropColumn('yetkili_user_id');
            // customer_id'yi silmiyoruz, o kalıcı olabilir
        });
    }
};