<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('iaa_resimler', function (Blueprint $table) {
        $table->softDeletes(); // deleted_at sütununu ekler
    });
}

public function down(): void
{
    Schema::table('iaa_resimler', function (Blueprint $table) {
        $table->dropSoftDeletes(); // deleted_at sütununu kaldırır
    });
}
};
