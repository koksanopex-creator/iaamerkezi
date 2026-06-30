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
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->string('return_date_revision_status')->nullable()->after('estimated_return_date')->comment('Dönüş Tarihi Revizyon Durumu (Yok, Bekliyor, Onaylandı, Reddedildi, Revizyon İsteniyor)');
            $table->date('return_date_revision_requested_date')->nullable()->after('return_date_revision_status');
            $table->text('return_date_revision_reason')->nullable()->after('return_date_revision_requested_date');
            $table->text('return_date_revision_response')->nullable()->after('return_date_revision_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->dropColumn([
                'return_date_revision_status',
                'return_date_revision_requested_date',
                'return_date_revision_reason',
                'return_date_revision_response'
            ]);
        });
    }
};
