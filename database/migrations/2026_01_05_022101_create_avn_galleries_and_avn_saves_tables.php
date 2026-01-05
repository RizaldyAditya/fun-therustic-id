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
        Schema::create('avn_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avn_id')->constrained()->cascadeOnDelete();
            $table->string('image_url');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('avn_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avn_id')->constrained()->cascadeOnDelete();
            $table->string('file_url');
            $table->string('label')->nullable();
            $table->string('version')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avn_galleries');
        Schema::dropIfExists('avn_saves');
    }
};
