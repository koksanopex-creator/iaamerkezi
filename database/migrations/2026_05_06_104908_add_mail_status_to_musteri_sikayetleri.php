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
            $table->boolean('mail_sent')->default(false)->after('notified_snapshot');
            $table->text('mail_error')->nullable()->after('mail_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->dropColumn(['mail_sent', 'mail_error']);
        });
    }
};
