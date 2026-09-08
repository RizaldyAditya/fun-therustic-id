<?php

namespace App\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait SociGames
{
    private static ?array $categories = null;

    /**
     * Fetch the latest items from SociGames via WordPress REST API.
     *
     * @param  string|null  $url  The category URL (used to determine category ID)
     * @return array<int, array{title: string, version: string, url: string, image: string, category: string, date: string, raw_title: string}>
     */
    public static function getLatestItems(?string $url): array
    {
        if (empty($url)) {
            return [];
        }

        try {
            $categoryId = self::getCategoryIdFromUrl($url);

            $response = self::apiGet('/wp-json/wp/v2/posts', [
                'categories' => $categoryId,
                'per_page' => 50,
                '_fields' => 'id,date,title,link,featured_media',
            ]);

            if ($response->failed()) {
                return [];
            }

            $posts = $response->json();

            if (empty($posts)) {
                return [];
            }

            // Resolve featured images
            $mediaIds = collect($posts)->pluck('featured_media')->filter()->unique()->values()->all();
            $images = self::resolveMediaUrls($mediaIds);

            $items = [];
            foreach ($posts as $post) {
                $rawTitle = html_entity_decode($post['title']['rendered'] ?? '');
                $parsed = self::parseTitle($rawTitle);

                $mediaId = $post['featured_media'] ?? null;
                $imageUrl = $mediaId ? ($images[$mediaId] ?? '') : '';

                $items[] = [
                    'title' => $parsed['title'],
                    'version' => $parsed['version'],
                    'url' => $post['link'] ?? '',
                    'image' => $imageUrl,
                    'category' => self::getCategoryName($categoryId),
                    'date' => $post['date'] ?? '',
                    'raw_title' => $rawTitle,
                ];
            }

            return $items;
        } catch (\Exception $e) {
            \Log::error('SociGames crawl failed: '.$e->getMessage(), [
                'url' => $url,
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Make an authenticated GET request to the SociGames API.
     */
    private static function apiGet(string $endpoint, array $params = []): Response
    {
        return Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        ])->timeout(15)->get('https://socigames.com'.$endpoint, $params);
    }

    /**
     * Extract the category ID from a SociGames category URL.
     */
    private static function getCategoryIdFromUrl(string $url): int
    {
        // Known category mappings
        $mappings = [
            'renpy' => 3411,
            'adult' => 473,
        ];

        foreach ($mappings as $slug => $id) {
            if (str_contains($url, '/'.$slug.'/')) {
                return $id;
            }
        }

        // Default to Ren'Py category
        return 3411;
    }

    /**
     * Resolve featured media IDs to their source URLs.
     *
     * @return array<int, string>
     */
    private static function resolveMediaUrls(array $mediaIds): array
    {
        if (empty($mediaIds)) {
            return [];
        }

        $images = [];

        // Fetch images sequentially to avoid rate limiting
        foreach ($mediaIds as $id) {
            try {
                $response = self::apiGet("/wp-json/wp/v2/media/{$id}", [
                    '_fields' => 'source_url',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $images[$id] = $data['source_url'] ?? '';
                }
            } catch (\Exception $e) {
                // Skip failed image resolutions
            }
        }

        return $images;
    }

    /**
     * Parse a title to extract the game name and version from bracket notation.
     *
     * Examples:
     *   "Defending Lydia Collier [v0.21.1]"  → title: "Defending Lydia Collier", version: "0.21.1"
     *   "Tune In to the Show [P2 Ep.12]"     → title: "Tune In to the Show", version: "P2 Ep.12"
     *   "Some Game"                           → title: "Some Game", version: ""
     *
     * @return array{title: string, version: string}
     */
    private static function parseTitle(string $rawTitle): array
    {
        $rawTitle = trim($rawTitle);

        // Match the last bracket group: [...]
        if (preg_match('/\[([^\]]+)\]\s*$/', $rawTitle, $matches)) {
            $title = trim(substr($rawTitle, 0, -strlen($matches[0])));
            $version = trim($matches[1]);

            // Strip leading "v" if present (e.g., "v0.21.1" → "0.21.1")
            $version = preg_replace('/^v/i', '', $version);

            return [
                'title' => $title,
                'version' => $version,
            ];
        }

        return [
            'title' => $rawTitle,
            'version' => '',
        ];
    }

    /**
     * Get the category name from its ID.
     */
    private static function getCategoryName(int $categoryId): string
    {
        $names = [
            3411 => 'Ren\'Py',
            473 => 'Adult',
        ];

        return $names[$categoryId] ?? 'Unknown';
    }
}
