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
        Schema::table('donghuas', function (Blueprint $table) {
            $table->string('local_download_path')->nullable()->after('episode_dl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donghuas', function (Blueprint $table) {
            $table->dropColumn('local_download_path');
        });
    }
};
