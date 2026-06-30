<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disiplin_kurulu_toplanti_katilimci', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toplanti_id')->constrained('disiplin_kurulu_toplanti')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Dışarıdan davet edilen için (sistemde kullanıcı olmayabilir)
            $table->string('dis_katilimci_adi')->nullable();
            $table->string('dis_katilimci_email')->nullable();
            $table->enum('rol', ['organizator', 'katilimci', 'davetli', 'gozlemci'])->default('katilimci');
            $table->enum('katilim_durumu', ['bekleniyor', 'kabul_edildi', 'reddedildi', 'katildi', 'katilmadi'])->default('bekleniyor');
            $table->timestamp('davet_gonderildi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disiplin_kurulu_toplanti_katilimci');
    }
};
