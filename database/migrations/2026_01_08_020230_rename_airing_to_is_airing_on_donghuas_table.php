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
            $table->renameColumn('airing', 'is_airing');
            $table->renameColumn('is_observed', 'is_hot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donghuas', function (Blueprint $table) {
            $table->renameColumn('is_airing', 'airing');
            $table->renameColumn('is_hot', 'is_observed');
        });
    }
};
