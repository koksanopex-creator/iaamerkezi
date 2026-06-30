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
            $table->json('visitors')->nullable()->after('visitor_id')->comment('Ziyarete gidecek birden fazla personelin ID listesi');
        });

        // Mevcut verileri yeni sütuna aktar
        \Illuminate\Support\Facades\DB::table('iaa_ziyaret_planlari')
            ->whereNotNull('visitor_id')
            ->orderBy('id')
            ->chunk(100, function ($records) {
                foreach ($records as $record) {
                    \Illuminate\Support\Facades\DB::table('iaa_ziyaret_planlari')
                        ->where('id', $record->id)
                        ->update([
                            'visitors' => json_encode([(string) $record->visitor_id])
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->dropColumn('visitors');
        });
    }
};
