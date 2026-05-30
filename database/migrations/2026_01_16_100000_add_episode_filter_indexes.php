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
        Schema::table('donghua_episodes', function (Blueprint $table) {
            // Add index on donghua_id for filtering by donghua
            $table->index('donghua_id', 'episodes_donghua_id_index');

            // Add index on stream_id for filtering by stream source
            $table->index('stream_id', 'episodes_stream_id_index');

            // Add index on title for search queries
            $table->index('title', 'episodes_title_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropIndex('episodes_donghua_id_index');
            $table->dropIndex('episodes_stream_id_index');
            $table->dropIndex('episodes_title_index');
        });
    }
};
