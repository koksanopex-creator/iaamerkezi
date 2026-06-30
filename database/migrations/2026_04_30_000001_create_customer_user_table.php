<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pivot tablosunu oluştur
        Schema::create('customer_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true); // Firmaya özel aktiflik durumu
            $table->timestamps();

            $table->unique(['customer_id', 'user_id']);
        });

        // 2. Mevcut verileri taşı (users tablosundaki customer_id'leri pivot tabloya aktar)
        $usersWithCustomer = DB::table('users')
            ->whereNotNull('customer_id')
            ->select('id', 'customer_id', 'created_at', 'updated_at')
            ->get();

        foreach ($usersWithCustomer as $user) {
            DB::table('customer_user')->insert([
                'user_id' => $user->id,
                'customer_id' => $user->customer_id,
                'is_active' => true,
                'created_at' => $user->created_at ?: now(),
                'updated_at' => $user->updated_at ?: now(),
            ]);
        }
        
        // NOT: users.customer_id sütununu hemen silmiyoruz (Backward Compatibility için)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_user');
    }
};
