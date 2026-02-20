<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sikayet_teknik_detaylari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('musteri_sikayeti_id');
            $table->string('lot_no')->nullable();
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->unsignedBigInteger('genel_hammadde_id')->nullable();
            $table->unsignedBigInteger('urun_versiyonu_id')->nullable();
            $table->timestamps();

            // İlişkiler
            $table->foreign('musteri_sikayeti_id')->references('id')->on('musteri_sikayetleri')->onDelete('cascade');
            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('set null');
            $table->foreign('genel_hammadde_id')->references('id')->on('genel_hammaddeler')->onDelete('set null');
            $table->foreign('urun_versiyonu_id')->references('id')->on('urun_versiyonlari')->onDelete('set null');
        });

        // MEVCUT VERİLERİ TAŞI (Data Migration)
        $sikayetler = \Illuminate\Support\Facades\DB::table('musteri_sikayetleri')
            ->whereNotNull('lot_no')
            ->orWhereNotNull('machine_id')
            ->orWhereNotNull('genel_hammadde_id')
            ->orWhereNotNull('urun_versiyonu_id')
            ->get();

        foreach ($sikayetler as $sikayet) {
            \Illuminate\Support\Facades\DB::table('sikayet_teknik_detaylari')->insert([
                'musteri_sikayeti_id' => $sikayet->id,
                'lot_no' => $sikayet->lot_no,
                'machine_id' => $sikayet->machine_id,
                'genel_hammadde_id' => $sikayet->genel_hammadde_id,
                'urun_versiyonu_id' => $sikayet->urun_versiyonu_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ESKİ KOLONLARI KALDIRABİLİRİZ (Opsiyonel ama temizlik için iyi olur)
        // Ancak geri dönüş gerekirse diye şimdilik null yapılabilir veya tamamen kaldırılabilir.
        // Güvenlik için kolonları şimdilik tutuyoruz ama nullable yapıyoruz.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sikayet_teknik_detaylari');
    }
};
