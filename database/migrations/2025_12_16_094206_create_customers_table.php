<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Firma Adı (Örn: Coca Cola A.Ş.)
            $table->string('tax_number')->nullable(); // Vergi No
            $table->string('tax_office')->nullable(); // Vergi Dairesi
            $table->text('address')->nullable();
            $table->string('phone')->nullable(); // Santral Telefonu
            $table->string('email')->nullable(); // Genel Firma E-postası
            
            // Yurt İçi / Yurt Dışı Ayrımı
            $table->string('location_type')->default('Yurt İçi'); 
            
            $table->boolean('is_active')->default(true); // Aktif/Pasif
            $table->timestamps();
            $table->softDeletes(); // Silinirse çöp kutusuna gitsin
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};