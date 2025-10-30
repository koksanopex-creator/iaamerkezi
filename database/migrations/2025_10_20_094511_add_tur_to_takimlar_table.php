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
        Schema::table('takimlar', function (Blueprint $table) {
            // 'iaa' veya 'sikayet' gibi değerler alacak bir sütun ekliyoruz.
            // Varsayılan olarak 'iaa' atıyoruz ki mevcut takımlarınız doğru kategoride kalsın.
            $table->string('tur')->default('iaa')->after('kurallar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('takimlar', function (Blueprint $table) {
            //
        });
    }
};
