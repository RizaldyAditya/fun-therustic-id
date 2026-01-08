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
            $table->text('synopsis')->nullable()->after('title_zh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donghuas', function (Blueprint $table) {
            $table->dropColumn('synopsis');
        });
    }
};
