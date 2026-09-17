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
        Schema::create('anime7_rss', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->datetime('post_date')->nullable();
            $table->string('url');
            $table->text('cover_image_url')->nullable();
            $table->longText('content');
            $table->timestamps();

            $table->unique('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime7_rss');
    }
};
