<?php

namespace App\Console\Commands;

use App\Models\Anime7Rss;
use App\Models\VarEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CrawlAnime7 extends Command
{
    protected $signature = 'app:crawl-anime7';

    protected $description = 'Crawl Anime7 WordPress REST API and store latest updates.';

    public function handle(): int
    {
        $url = VarEntry::where('name', 'anime7_rss_url')->value('value');

        if (empty($url)) {
            $this->error('No Anime7 URL configured in vars table (anime7_rss_url).');

            return 1;
        }

        $posts = $this->fetchPosts($url);

        if (empty($posts)) {
            $this->warn('No posts found.');

            return 0;
        }

        $posts = array_reverse($posts);

        $created = 0;
        $skipped = 0;

        foreach ($posts as $post) {
            $title = html_entity_decode($post['title']['rendered'] ?? '');
            $link = $post['link'] ?? '';
            $contentHtml = $post['content']['rendered'] ?? '';
            $postDate = $post['modified'] ?? null;

            if (empty($link)) {
                $skipped++;

                continue;
            }

            $coverImageUrl = $this->parseCoverImage($contentHtml);

            Anime7Rss::updateOrCreate(
                ['url' => $link],
                [
                    'title' => $title,
                    'post_date' => $postDate ? date('Y-m-d H:i:s', strtotime($postDate)) : null,
                    'cover_image_url' => $coverImageUrl,
                    'content' => $contentHtml,
                ]
            );

            $created++;
        }

        $total = Anime7Rss::count();
        $this->info("Crawl completed. Processed: {$created} posts. Skipped: {$skipped}. Total in database: {$total}");

        return 0;
    }

    private function fetchPosts(string $url): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ])->timeout(15)->get($url);

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

    private function parseCoverImage(string $contentHtml): ?string
    {
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $contentHtml, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
