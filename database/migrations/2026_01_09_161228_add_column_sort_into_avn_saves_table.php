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
        Schema::table('avn_saves', function (Blueprint $table) {
            $table->integer('sort')->default(0)->after('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avn_saves', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
