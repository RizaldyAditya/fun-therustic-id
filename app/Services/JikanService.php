<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JikanService
{
    public function searchAnime(string $query): array
    {
        return [];
    }

    public function getAnimeData(string $url): ?array
    {
        preg_match('/anime\/(\d+)/', $url, $matches);
        $malId = $matches[1] ?? null;
        if (!$malId) {
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
            'title' => $data['title'] ?? '',
            'title_jp' => $data['title_japanese'] ?? '',
            'synopsis' => $data['synopsis'] ?? '',
            'poster' => $data['images']['jpg']['large_image_url'] ?? null,
            'type' => $data['type'] ?? 'TV',
            'genres' => $genres,
            'studios' => $data['studios'] ?? [],
            'source' => $data['source'] ?? '',
            'season' => $data['season'] ?? '',
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
}
