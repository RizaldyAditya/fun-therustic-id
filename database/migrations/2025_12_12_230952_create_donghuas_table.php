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
        Schema::create('donghuas', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('title_en')->default('');
            $table->string('title_zh')->default('');
            $table->json('external_titles')->nullable();
            $table->integer('season')->default(1)->nullable();
            $table->integer('episode_latest')->default(0)->nullable();
            $table->integer('episode_watched')->default(0)->nullable();
            $table->integer('episode_watched_seasonal')->default(0)->nullable();
            $table->integer('episode_total')->default(0)->nullable();
            $table->integer('episode_dl')->default(0)->nullable();
            $table->integer('status_id')->nullable();
            $table->boolean('is_airing')->default(0);
            $table->boolean('is_hot')->default(0);
            $table->string('myanimelist')->nullable();
            $table->text('image_cover')->nullable();
            $table->string('mc_name')->nullable();
            $table->string('mc_wikia')->nullable();
            $table->integer('studio_id')->nullable();
            $table->integer('source_id')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donghuas');
    }
};
