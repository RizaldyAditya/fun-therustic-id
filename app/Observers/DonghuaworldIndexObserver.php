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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class DonghuaworldIndexObserver extends CrawlObserver
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

        $html = (string) $response->getBody();
        $domCrawler = new DomCrawler($html);

        $stream = Stream::where('label', 'donghuaworld')->first();

        if ($stream->is_cover_image) {
            $donghuaImage = $domCrawler->filter('.thumbook > .thumb > img')->attr('src');
            Log::info($donghuaImage);
            $donghuaImage = strtok($donghuaImage ?? '', '?');

            $coverResponse = Http::get($donghuaImage);
            if (! $donghua->image_cover && $coverResponse->successful()) {
                $extension = pathinfo(parse_url($donghuaImage, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'img/covers/'.Str::uuid().'.'.$extension;
                Storage::disk('public')->put($fileName, $coverResponse->body());

                Donghua::where('id', $this->donghuaId)
                    ->update([
                        'image_cover' => $fileName ?? '',
                    ]);
            }
        }

        $domCrawler->filter('.eplister > ul > li')->each(function (DomCrawler $node) use ($stream) {
            try {
                Sleep::for(200)->milliseconds();

                $episodeLink = $node->filter('a')->attr('href');
                $episodeTitle = $node->filter('a > .epl-title')->text();

                $episodeNumberText = $node->filter('a > .epl-num')->text();
                $parsed = $this->sanitizeEpisodeNumber($episodeNumberText);
                $episodeNumber = $parsed['display'];

                $crawlerEpisodeLink = new DomCrawler($this->client->get($episodeLink)->getBody()->getContents());

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

                $serverLinks = $crawlerEpisodeLink->filter('.player-servers .server-item > a[data-hash]');

                if ($serverLinks->count() > 0) {
                    $serverLinks->each(function (DomCrawler $link) use (&$videoSourceUrlJson) {
                        $hashValue = $link->attr('data-hash');

                        if ($hashValue) {
                            try {
                                $decoded = base64_decode($hashValue);

                                if (preg_match('/src="([^"]+)"/', $decoded, $matches)) {
                                    $url = $matches[1];

                                    if (str_starts_with($url, '//')) {
                                        $url = 'https:'.$url;
                                    }

                                    if (str_contains($url, 'dailymotion')) {
                                        $videoSourceUrlJson['english']['dailymotion'] = $url;
                                    } else {
                                        $videoSourceUrlJson['english']['ok_ru'] = $url;
                                    }
                                }
                            } catch (\Exception $e) {
                                // Skip invalid base64
                            }
                        }
                    });
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
                Log::warning('Failed to parse a node: '.$e->getMessage());
            }
        });
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
