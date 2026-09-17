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
            $table->renameColumn('last_updated_on_itch', 'last_updated_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avns', function (Blueprint $table) {
            $table->renameColumn('last_updated_date', 'last_updated_on_itch');
        });
    }
};
