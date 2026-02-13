<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get all episodes with video_source_url
        $episodes = DB::table('donghua_episodes')
            ->whereNotNull('video_source_url')
            ->where('video_source_url', '!=', '')
            ->get(['id', 'video_source_url']);

        foreach ($episodes as $episode) {
            $url = json_decode($episode->video_source_url, true);

            // Check if 'ok.ru' exists and convert to 'ok_ru'
            if (isset($url['english']['ok.ru'])) {
                $url['english']['ok_ru'] = $url['english']['ok.ru'];
                unset($url['english']['ok.ru']);
            }

            if (isset($url['indonesia']['ok.ru'])) {
                $url['indonesia']['ok_ru'] = $url['indonesia']['ok.ru'];
                unset($url['indonesia']['ok.ru']);
            }

            DB::table('donghua_episodes')
                ->where('id', $episode->id)
                ->update(['video_source_url' => json_encode($url)]);
        }
    }

    public function down(): void
    {
        // Revert 'ok_ru' back to 'ok.ru'
        $episodes = DB::table('donghua_episodes')
            ->whereNotNull('video_source_url')
            ->where('video_source_url', '!=', '')
            ->get(['id', 'video_source_url']);

        foreach ($episodes as $episode) {
            $url = json_decode($episode->video_source_url, true);

            if (isset($url['english']['ok_ru'])) {
                $url['english']['ok.ru'] = $url['english']['ok_ru'];
                unset($url['english']['ok_ru']);
            }

            if (isset($url['indonesia']['ok_ru'])) {
                $url['indonesia']['ok.ru'] = $url['indonesia']['ok_ru'];
                unset($url['indonesia']['ok_ru']);
            }

            DB::table('donghua_episodes')
                ->where('id', $episode->id)
                ->update(['video_source_url' => json_encode($url)]);
        }
    }
};
