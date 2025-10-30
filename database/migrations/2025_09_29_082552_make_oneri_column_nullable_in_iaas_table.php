<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            // 'oneri' kolonunun yapısını 'nullable' (boş bırakılabilir) olarak değiştiriyoruz.
            $table->text('oneri')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            // Geri alma işlemi için, kolonu tekrar 'nullable' olmayan hale getiriyoruz.
            $table->text('oneri')->nullable(false)->change();
        });
    }
};