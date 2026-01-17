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
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_jp')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('poster')->nullable();
            $table->string('type')->default('TV')->nullable();
            $table->integer('genre_id')->nullable();
            $table->integer('studio_id')->nullable();
            $table->integer('source_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('season')->nullable();
            $table->integer('year')->nullable();
            $table->string('broadcast_day')->nullable();
            $table->integer('episode_total')->nullable();
            $table->integer('episode_watched')->nullable();
            $table->integer('episode_downloaded')->nullable();
            $table->string('myanimelist_url')->nullable()->unique();
            $table->float('myanimelist_score')->nullable();
            $table->date('air_date')->nullable();
            $table->json('attributes')->nullable();
            $table->boolean('is_hot')->default(0);
            $table->boolean('is_airing')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
