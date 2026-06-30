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
        Schema::create('disiplin_case_toplanti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toplanti_id')->constrained('disiplin_kurulu_toplanti')->cascadeOnDelete();
            $table->foreignId('case_id')->constrained('disciplinary_cases')->cascadeOnDelete();
            $table->timestamps();
            
            // Aynı dosya aynı toplantıya birden fazla kez eklenmesin
            $table->unique(['toplanti_id', 'case_id']);
        });

        // Veri Aktarımı (Mevcut 'disciplinary_case_id' verilerini pivot tabloya taşı)
        $mevcutToplantilar = DB::table('disiplin_kurulu_toplanti')
            ->whereNotNull('disciplinary_case_id')
            ->get();

        foreach ($mevcutToplantilar as $toplanti) {
            DB::table('disiplin_case_toplanti')->insertOrIgnore([
                'toplanti_id' => $toplanti->id,
                'case_id'     => $toplanti->disciplinary_case_id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disiplin_case_toplanti');
    }
};
