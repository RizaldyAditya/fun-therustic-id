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
        Schema::create('avns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('developer')->nullable();
            $table->string('version')->nullable();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('itch_io_url')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamp('last_updated_on_itch')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avns');
    }
};
