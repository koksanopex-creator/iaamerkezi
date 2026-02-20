<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('iaa_talepleri', function (Blueprint $table) {
            $table->unsignedBigInteger('takim_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eğer null değerler varsa, rollback sırasında hata almamak için önlem:
        // Null olanları temizle (uyarı: veri kaybı olur, bu yüzden geliştirme ortamı için ok)
        DB::table('iaa_talepleri')->whereNull('takim_id')->delete();

        Schema::table('iaa_talepleri', function (Blueprint $table) {
            $table->unsignedBigInteger('takim_id')->nullable(false)->change();
        });
    }
};
