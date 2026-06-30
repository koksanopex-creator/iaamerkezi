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
        Schema::table('sikayet_hatirlatici_kurallari', function (Blueprint $table) {
            $table->string('mail_konusu')->nullable()->after('ek_kullanici_ids');
            $table->text('mail_taslagi')->nullable()->after('mail_konusu');
            $table->text('bildirim_metni')->nullable()->after('mail_taslagi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sikayet_hatirlatici_kurallari', function (Blueprint $table) {
            $table->dropColumn(['mail_konusu', 'mail_taslagi', 'bildirim_metni']);
        });
    }
};
