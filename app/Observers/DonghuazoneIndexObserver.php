<?php

namespace App\Observers;

ini_set('max_execution_time', 0); // no time limit()

use App\Models\Donghua;
use App\Models\Episode;
use App\Models\Stream;
use App\Traits\Utilities;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class DonghuazoneIndexObserver extends CrawlObserver
{
    use Utilities;

    protected $client;

    public function __construct(protected ?int $donghuaId = null)
    {
        $this->client = new Client;
    }

    /**
     * SUCCESS: Must match exactly.
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null// Ensure this is nullable string
    ): void {
        Log::info("Crawling: {$url}");

        if (! $this->donghuaId) {
            return;
        }

        $donghua = Donghua::find($this->donghuaId);
        if (! $donghua) {
            return;
        }

        $stream = Stream::where('label', 'donghuazone')->first();

        // Fetch episode list from Blogger JSON feed API
        $label = $donghua->external_titles['donghuazone_title'] ?? $donghua->title_en;
        if (empty($label)) {
            Log::warning('DonghuazoneIndex: No label found for donghua ID '.$this->donghuaId);

            return;
        }

        $startIndex = 1;
        $maxResults = 50;
        $hasMore = true;

        while ($hasMore) {
            $feedUrl = 'https://www.donghuazone.com/feeds/posts/default/-/'.urlencode($label).'?alt=json&start-index='.$startIndex.'&max-results='.$maxResults;

            try {
                $feedResponse = Http::timeout(30)->get($feedUrl);
                if (! $feedResponse->successful()) {
                    Log::warning('DonghuazoneIndex: Failed to fetch feed for '.$label);

                    return;
                }

                $feedData = $feedResponse->json();
                $entries = $feedData['feed']['entry'] ?? [];
                $totalResults = (int) ($feedData['feed']['openSearch$totalResults']['$t'] ?? 0);

                if (empty($entries)) {
                    break;
                }

                foreach ($entries as $entry) {
                    try {
                        Sleep::for(200)->milliseconds();

                        $episodeTitle = $entry['title']['$t'] ?? '';

                        // Get episode URL from alternate link
                        $episodeLink = null;
                        foreach ($entry['link'] ?? [] as $link) {
                            if (($link['rel'] ?? '') === 'alternate' && ($link['type'] ?? '') === 'text/html') {
                                $episodeLink = $link['href'];
                                break;
                            }
                        }

                        if (empty($episodeLink)) {
                            continue;
                        }

                        // Extract episode number from title (e.g. "Shrouding The Heavens Episode 181 [4K | Indo Sub] – English Sub")
                        $episodeNumberText = $episodeTitle;
                        if (preg_match('/episode\s+(\d+)/i', $episodeTitle, $matches)) {
                            $episodeNumberText = $matches[1];
                        }
                        $parsed = $this->sanitizeEpisodeNumber($episodeNumberText);
                        $episodeNumber = $parsed['display'];

                        // Extract video source URL from content HTML
                        $contentHtml = $entry['content']['$t'] ?? '';
                        $videoSourceUrlJson = [
                            'english' => [
                                'dailymotion' => null,
                                'ok_ru' => null,
                            ],
                            'indonesia' => [
                                'dailymotion' => null,
                                'ok_ru' => null,
                            ],
                        ];

                        // Extract Dailymotion URLs from server button onclick attributes
                        if (preg_match_all('/changeServer\(this,\s*[\'"](.+?)[\'"]\)/', $contentHtml, $matches)) {
                            foreach ($matches[1] as $videoUrl) {
                                if (! empty($videoUrl)) {
                                    if (str_starts_with($videoUrl, '//')) {
                                        $videoUrl = 'https:'.$videoUrl;
                                    }

                                    if (str_contains($videoUrl, 'dailymotion')) {
                                        $videoSourceUrlJson['english']['dailymotion'] = $videoUrl;
                                    } else {
                                        $videoSourceUrlJson['english']['ok_ru'] = $videoUrl;
                                    }
                                }
                            }
                        }

                        $hasValidUrl = ! empty($videoSourceUrlJson['english']['dailymotion'])
                            || ! empty($videoSourceUrlJson['english']['ok_ru'])
                            || ! empty($videoSourceUrlJson['indonesia']['dailymotion'])
                            || ! empty($videoSourceUrlJson['indonesia']['ok_ru']);

                        if ($hasValidUrl) {
                            Episode::firstOrCreate(
                                ['stream_url' => $episodeLink],
                                [
                                    'donghua_id' => $this->donghuaId,
                                    'title' => trim($episodeTitle),
                                    'stream_id' => $stream->id,
                                    'episode_number' => $episodeNumber,
                                    'video_source_url' => $videoSourceUrlJson,
                                    'is_an_update' => false,
                                ]
                            );
                        }
                    } catch (\Exception $e) {
                        Log::warning('DonghuazoneIndex: Failed to parse entry: '.$e->getMessage());
                    }
                }

                // Check if there are more pages
                $startIndex += $maxResults;
                $hasMore = $startIndex <= $totalResults;

            } catch (\Exception $e) {
                Log::error('DonghuazoneIndex: Failed to fetch feed: '.$e->getMessage());
                $hasMore = false;
            }
        }
    }

    /**
     * FAILURE: This is where your error is.
     * It MUST use RequestException and match all nullable types.
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null// Ensure this is nullable string
    ): void {
        Log::error("Crawl failed: {$url}. Message: {$requestException->getMessage()}");
    }

    /**
     * Optional: Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        // Log::info("Crawl session completed successfully.");
    }
}
