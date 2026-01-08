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
            $table->integer('trending_sort')->default(99)->after('is_hot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donghuas', function (Blueprint $table) {
            $table->dropColumn('trending_sort');
        });
    }
};
