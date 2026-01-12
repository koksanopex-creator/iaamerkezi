<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arabuluculuk_files', function (Blueprint $table) {
            // Eğer sütunlar yoksa ekle
            
            if (!Schema::hasColumn('arabuluculuk_files', 'mime_tipi')) {
                $table->string('mime_tipi')->nullable()->after('orijinal_adi');
            }

            if (!Schema::hasColumn('arabuluculuk_files', 'version_no')) {
                $table->integer('version_no')->default(1)->after('orijinal_adi');
            }

            if (!Schema::hasColumn('arabuluculuk_files', 'locked')) {
                $table->boolean('locked')->default(false)->after('is_active');
            }

            if (!Schema::hasColumn('arabuluculuk_files', 'archived_at')) {
                $table->dateTime('archived_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arabuluculuk_files', function (Blueprint $table) {
            $table->dropColumn(['mime_tipi', 'version_no', 'locked', 'archived_at']);
        });
    }
};