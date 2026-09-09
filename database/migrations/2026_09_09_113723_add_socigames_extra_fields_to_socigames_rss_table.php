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
        Schema::table('socigames_rss', function (Blueprint $table) {
            $table->string('developer')->nullable()->after('version');
            $table->text('description')->nullable()->after('developer');
            $table->text('genres')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('socigames_rss', function (Blueprint $table) {
            $table->dropColumn(['developer', 'description', 'genres']);
        });
    }
};
