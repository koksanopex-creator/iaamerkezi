<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // boolean -> true/false (1/0)
            // default(false) -> Yeni kaydolan herkes varsayılan olarak "onaylanmamış" olur.
            $table->boolean('onaylandi_mi')->default(false)->after('bolum_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onaylandi_mi');
        });
    }
};
