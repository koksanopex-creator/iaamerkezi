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
        Schema::table('customer_user', function (Blueprint $table) {
            $table->string('unvan')->nullable()->after('user_id');
        });

        // Mevcut verileri taşı (users tablosundaki unvanları pivot tabloya kopyala)
        $pivots = \Illuminate\Support\Facades\DB::table('customer_user')->get();
        foreach ($pivots as $pivot) {
            $user = \Illuminate\Support\Facades\DB::table('users')->find($pivot->user_id);
            if ($user && $user->unvan) {
                \Illuminate\Support\Facades\DB::table('customer_user')
                    ->where('id', $pivot->id)
                    ->update(['unvan' => $user->unvan]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_user', function (Blueprint $table) {
            $table->dropColumn('unvan');
        });
    }
};
