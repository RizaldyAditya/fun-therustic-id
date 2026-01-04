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
            $table->string('developer')->nullable();
            $table->string('version')->nullable();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('rating')->nullable()->default(0);
            $table->string('itch_io_url')->nullable();
            $table->string('cover_image')->nullable();
            $table->foreignId('genre_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('last_updated_on_itch')->nullable();
            $table->string('last_played_version')->nullable();
            $table->text('saves_file_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
