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
        Schema::table('users', function (Blueprint $table) {
            // 'email' sütunundan sonra, 'bolumler' tablosuna bağlı bir sütun ekle.
            // nullable() -> Bir kullanıcının bölümü olmak zorunda değil (örn: Superadmin).
            // constrained() -> Veritabanı bütünlüğünü sağlar.
            // nullOnDelete() -> İlişkili bölüm silinirse bu alanı NULL yapar.
            $table->foreignId('bolum_id')->nullable()->after('email')->constrained('bolumler')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Geri alma işleminde bu sütunu ve anahtarı kaldır.
            $table->dropForeign(['bolum_id']);
            $table->dropColumn('bolum_id');
        });
    }
};