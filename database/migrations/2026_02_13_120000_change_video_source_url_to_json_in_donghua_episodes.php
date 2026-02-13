<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donghua_episodes', function (Blueprint $table) {
            $table->json('video_source_url_new')->nullable()->after('video_source_url');
        });

        DB::statement("
            UPDATE donghua_episodes 
            SET video_source_url_new = JSON_OBJECT(
                'english', JSON_OBJECT(
                    'dailymotion', CASE 
                        WHEN video_source_url LIKE '%dailymotion%' THEN video_source_url 
                        ELSE NULL 
                    END,
                    'ok.ru', CASE 
                        WHEN video_source_url LIKE '%ok.ru%' THEN video_source_url 
                        ELSE NULL 
                    END
                ),
                'indonesia', JSON_OBJECT(
                    'dailymotion', NULL,
                    'ok.ru', NULL
                )
            )
            WHERE video_source_url IS NOT NULL 
              AND video_source_url != ''
        ");

        Schema::table('donghua_episodes', function (Blueprint $table) {
            $table->dropColumn('video_source_url');
            $table->renameColumn('video_source_url_new', 'video_source_url');
        });
    }

    public function down(): void
    {
        Schema::table('donghua_episodes', function (Blueprint $table) {
            $table->string('video_source_url')->nullable();
        });

        DB::statement("
            UPDATE donghua_episodes 
            SET video_source_url = COALESCE(
                JSON_UNQUOTE(JSON_EXTRACT(video_source_url, '$.english.dailymotion')),
                JSON_UNQUOTE(JSON_EXTRACT(video_source_url, '$.english.ok.ru'))
            )
            WHERE video_source_url IS NOT NULL
        ");

        Schema::table('donghua_episodes', function (Blueprint $table) {
            $table->dropColumn('video_source_url_new');
        });
    }
};
