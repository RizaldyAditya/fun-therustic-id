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
        Schema::table('avns', function (Blueprint $table) {
            $table->string('socigames_url')->nullable()->after('itch_io_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avns', function (Blueprint $table) {
            $table->dropColumn('socigames_url');
        });
    }
};
