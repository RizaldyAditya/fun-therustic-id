<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socigames_rss', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('url');
            $table->string('version')->nullable();
            $table->text('cover_image')->nullable();
            $table->date('release_date')->nullable();
            $table->timestamps();

            $table->unique(['title', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socigames_rss');
    }
};
