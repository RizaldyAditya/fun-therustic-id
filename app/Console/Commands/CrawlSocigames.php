<?php

namespace App\Console\Commands;

use App\Models\SocigamesRss;
use App\Models\VarEntry;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CrawlSocigames extends Command
{
    protected $signature = 'app:crawl-socigames';

    protected $description = 'Crawl SociGames WordPress REST API and store latest updates.';

    private const CATEGORY_IDS = [
        'renpy' => 3411,
        'adult' => 473,
    ];

    public function handle(): int
    {
        $url = VarEntry::where('name', 'socigames_renpy_update')->value('value');

        if (empty($url)) {
            $this->error('No SociGames URL configured in vars table (socigames_renpy_update).');

            return 1;
        }

        $categoryId = $this->getCategoryIdFromUrl($url);

        $this->info("Crawling SociGames category ID: {$categoryId}");

        $posts = $this->fetchPosts($categoryId);

        if (empty($posts)) {
            $this->warn('No posts found.');

            return 0;
        }

        $mediaIds = collect($posts)
            ->pluck('featured_media')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $images = $this->resolveMediaUrls($mediaIds);

        $created = 0;
        $skipped = 0;

        foreach ($posts as $post) {
            $rawTitle = html_entity_decode($post['title']['rendered'] ?? '');
            $parsed = $this->parseTitle($rawTitle);

            $mediaId = $post['featured_media'] ?? null;
            $coverImage = $mediaId ? ($images[$mediaId] ?? null) : null;

            $releaseDate = $post['date'] ? date('Y-m-d', strtotime($post['date'])) : null;

            SocigamesRss::updateOrCreate(
                ['title' => $parsed['title'], 'version' => $parsed['version']],
                [
                    'url' => $post['link'] ?? '',
                    'cover_image' => $coverImage,
                    'release_date' => $releaseDate,
                ]
            );

            $created++;
        }

        $total = SocigamesRss::count();
        $this->info("Crawl completed. Processed: {$created} posts. Total in database: {$total}");

        return 0;
    }

    private function fetchPosts(int $categoryId): array
    {
        try {
            $response = $this->apiGet('/wp-json/wp/v2/posts', [
                'categories' => $categoryId,
                'per_page' => 50,
                '_fields' => 'id,date,title,link,featured_media',
            ]);

            if ($response->failed()) {
                $this->error('API request failed: '.$response->status());

                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            $this->error('API request error: '.$e->getMessage());

            return [];
        }
    }

    private function apiGet(string $endpoint, array $params = []): Response
    {
        return Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        ])->timeout(15)->get('https://socigames.com'.$endpoint, $params);
    }

    private function getCategoryIdFromUrl(string $url): int
    {
        foreach (self::CATEGORY_IDS as $slug => $id) {
            if (str_contains($url, '/'.$slug.'/')) {
                return $id;
            }
        }

        return 3411;
    }

    /**
     * @return array<int, string>
     */
    private function resolveMediaUrls(array $mediaIds): array
    {
        if (empty($mediaIds)) {
            return [];
        }

        $images = [];

        foreach ($mediaIds as $id) {
            try {
                $response = $this->apiGet("/wp-json/wp/v2/media/{$id}", [
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

    private function parseTitle(string $rawTitle): array
    {
        $rawTitle = trim($rawTitle);

        if (preg_match('/\[([^\]]+)\]\s*$/', $rawTitle, $matches)) {
            $title = trim(substr($rawTitle, 0, -strlen($matches[0])));
            $version = trim($matches[1]);
            $version = preg_replace('/^v/i', '', $version);

            return [
                'title' => $title,
                'version' => $version,
            ];
        }

        return [
            'title' => $rawTitle,
            'version' => null,
        ];
    }
}
