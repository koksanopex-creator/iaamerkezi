<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('iaa_progress_updates', function (Blueprint $table) {
            // Bu sütun, adımın müşteriden gizli olup olmadığını tutacak
            $table->boolean('is_hidden_from_customer')->default(false)->after('completed_at');
        });
    }

    public function down()
    {
        Schema::table('iaa_progress_updates', function (Blueprint $table) {
            $table->dropColumn('is_hidden_from_customer');
        });
    }
};
