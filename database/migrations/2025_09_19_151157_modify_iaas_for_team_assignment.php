<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            // Yeni `atanan_takim_id` sütununu ekliyoruz.
            $table->foreignId('atanan_takim_id')->nullable()->constrained('takimlar')->onDelete('set null');

            // Eski `atandi_user_id` ve `atanma_tarihi` sütunlarını kaldırıyoruz.
            // Not: Eğer bu sütunlarda önemli verileriniz varsa, bu adımı dikkatli atın. Ama yedek aldığınız için güvendeyiz.
            $table->dropForeign(['atandi_user_id']);
            $table->dropColumn('atandi_user_id');
            $table->dropColumn('atanma_tarihi');
        });
    }

    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            // Geri alma işlemi için eski sütunları tekrar ekliyoruz.
            $table->foreignId('atandi_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('atanma_tarihi')->nullable();

            // Yeni `atanan_takim_id` sütununu kaldırıyoruz.
            $table->dropForeign(['atanan_takim_id']);
            $table->dropColumn('atanan_takim_id');
        });
    }
};