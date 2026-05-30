<?php

namespace App\Services;

use App\Models\Anime;
use App\Models\Genre;
use App\Models\Source;
use App\Models\Studio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class JikanService
{
    /**
     * Get season data
     */
    public function getSeasonData(string $season, int $year): bool
    {
        $season_name = config('constant.season_name')[$season];
        $response = Http::get("https://api.jikan.moe/v4/seasons/{$year}/{$season_name}");
        if ($response->failed()) {
            return false;
        }
        $data = $response->json('data');
        $state = $this->importSeasonData($data);

        return $state;
    }

    /**
     * Get anime data
     */
    public function getAnimeData(string $url): ?array
    {
        preg_match('/anime\/(\d+)/', $url, $matches);
        $malId = $matches[1] ?? null;
        if (! $malId) {
            return null;
        }

        $response = Http::get("https://api.jikan.moe/v4/anime/{$malId}/full");
        if ($response->failed()) {
            return null;
        }

        $data = $response->json('data');

        $genres = [];
        foreach ($data['genres'] as $genre) {
            $genres[] = $genre['name'];
        }

        return [
            'title' => trim($data['title'] ?? ''),
            'title_jp' => trim($data['title_japanese'] ?? ''),
            'synopsis' => $data['synopsis'] ?? '',
            'poster' => $data['images']['jpg']['large_image_url'] ?? null,
            'type' => $data['type'] ?? 'TV',
            'genres' => $genres,
            'studios' => $data['studios'] ?? [],
            'source' => $data['source'] ?? '',
            'season' => config('constant.season_id')[$data['season']] ?? null,
            'year' => $data['year'] ?? 0,
            'broadcast_day' => $data['broadcast']['day'] ?? '',
            'status' => $data['status'] ?? '',
            'episodes' => $data['episodes'] ?? 0,
            'duration' => $data['duration'] ?? '',
            'myanimelist_score' => $data['score'] ?? 0,
            'aired' => $data['aired'] ?? '',
            'poster_path' => $data['images']['jpg']['large_image_url'] ?? null,
            'theme' => $data['theme'] ?? [],
            'external' => $data['external'] ?? [],
            'is_airing' => $data['airing'] ?? false,
        ];
    }

    /**
     * Import season data
     *
     * @param  array  $season
     */
    private function importSeasonData($season): bool
    {
        if (empty($season)) {
            return false;
        }

        foreach ($season as $data) {
            // title
            foreach ($data['titles'] as $v) {
                if ($v['type'] === 'Default') {
                    $title_default = trim($v['title'] ?? '');
                }
                if ($v['type'] === 'Japanese') {
                    $title_japanese = trim($v['title'] ?? '');
                }
            }

            // fetch poster and save it to local server
            $poster = $data['images']['webp']['large_image_url'];
            if ($poster) {
                $response = Http::get($poster);
                if ($response->failed()) {
                    return false;
                }
                $file_name = pathinfo($poster, PATHINFO_BASENAME);
                $file_path = 'img/anime-covers/'.$file_name;
                Storage::disk('public')->put($file_path, $response->body());
                $poster = $file_path;
            }

            // process studio
            $studio = $data['studios'][0]['name'] ?? null;
            $studio_id = null;
            if (! empty($studio)) {
                $studio_id = Studio::firstOrCreate(['name' => $studio])->id;
            }

            // process source
            $source_id = Source::firstOrCreate(['name' => $data['source']])->id;

            // process status
            $status_id = null;
            $is_airing = $data['airing'];
            $now = Carbon::now();
            $targetDate = Carbon::parse($data['aired']['from']);
            if ($is_airing) {
                $status_id = 1;
            } elseif (! $is_airing && $targetDate->greaterThan($now)) {
                $status_id = 12;
            } else {
                $status_id = 5;
            }

            // create anime
            $anime = Anime::updateOrCreate(['myanimelist_url' => $data['url']], [
                'title' => $title_default,
                'title_jp' => $title_japanese,
                'synopsis' => $data['synopsis'],
                'poster' => $poster,
                'type' => $data['type'],
                'studio_id' => $studio_id,
                'source_id' => $source_id,
                'status_id' => $status_id,
                'season' => config('constant.season_id')[$data['season']] ?? null,
                'year' => (int) $data['year'],
                'broadcast_day' => strtolower($data['broadcast']['day'] ?? ''),
                'episode_total' => $data['episodes'],
                'myanimelist_url' => $data['url'],
                'myanimelist_score' => $data['score'],
                'air_date' => Carbon::parse($data['aired']['from'])->format('Y-m-d') ?? $data['aired']['from'],
                'is_airing' => $data['airing'],
                'is_active' => true,
            ]);

            // process genres
            $genres = [];
            foreach ($data['genres'] as $genre) {
                $genres[] = $genre['name'];
            }
            $genres = array_unique($genres);
            foreach ($genres as $genre) {
                $genre_id = Genre::firstOrCreate(['name' => $genre])->id;
                $anime->genre()->attach($genre_id);
            }
        }

        return true;
    }

    /**
     * Fetch genres from Jikan API and save them to database.
     */
    public function getGenres(): bool
    {
        // fetch genres
        $response = Http::get('https://api.jikan.moe/v4/genres/anime');
        if ($response->failed()) {
            return false;
        }

        $genres = $response->json('data');
        foreach ($genres as $genre) {
            Genre::firstOrCreate(['name' => $genre['name']]);
        }

        return true;
    }
}
