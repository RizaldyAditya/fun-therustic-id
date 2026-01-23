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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('donghua_id')->nullable();
            $table->string('title')->nullable();
            $table->integer('episode_number')->nullable();
            $table->integer('stream_id')->nullable();
            $table->string('stream_url')->nullable();
            $table->string('video_source_url')->nullable(); 
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
