<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplinary_appeals', function (Blueprint $table) {
            $table->boolean('on_behalf')->default(false)->after('reason'); // Vekâleten mi?
            $table->foreignId('on_behalf_of_user_id')->nullable()->after('on_behalf')->constrained('users'); // Kimin adına yapıldı
        });
    }

    public function down(): void
    {
        Schema::table('disciplinary_appeals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('on_behalf_of_user_id');
            $table->dropColumn('on_behalf');
        });
    }
};
