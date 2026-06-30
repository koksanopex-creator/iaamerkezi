<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->boolean('visit_planned')->default(false)->after('durum');
        });
    }

    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropColumn('visit_planned');
        });
    }
};
