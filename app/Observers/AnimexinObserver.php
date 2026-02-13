<?php

namespace App\Observers;

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

class AnimexinObserver extends CrawlObserver
{
    use Utilities;

    protected $client;

    public function __construct()
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
        $html = (string) $response->getBody();
        $domCrawler = new DomCrawler($html);

        // Your parsing logic
        $domCrawler->filter('.listupd.normal > .excstf > article')->each(function (DomCrawler $node) {
            try {
                Sleep::for(200)->milliseconds();

                // get donghua title
                $donghuaTitle = $node->filter('.bsx > a.tip > .limit > .egghead > .eggtitle')->text();
                $donghuaTitle = preg_replace('/<([a-z][a-z0-9]*)[^>]*>.*?<\/\1>/is', '', trim($donghuaTitle ?? '')); // remove html tags and its

                // find donghua id
                $donghua_id = null;
                $donghua = Donghua::where('external_titles->animexin_title', trim($donghuaTitle ?? ''))->first();
                if ($donghua && $donghua->id) {
                    $donghua_id = $donghua->id;
                }

                if ($donghua_id) {
                    // find stream id
                    $stream = Stream::where('label', 'animexin')->first();

                    // get episode title
                    $episodeTitle = $node->filter('.bsx > a.tip')->attr('title');

                    // get episode link
                    $episodeLink = $node->filter('.bsx > a.tip')->attr('href');

                    // get episode number
                    $node_episode = $node->filter('.bsx > a.tip > .limit > .egghead > .eggmeta > .eggepisode');
                    $rawText = $node_episode->count() > 0 ? $node_episode->text() : '0';
                    $parsed = $this->sanitizeEpisodeNumber($rawText);
                    $episodeNumberPre = $parsed['int'];
                    $episodeNumber = $parsed['display'];

                    // get video source url from select dropdown
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

                    $select = $crawlerEpisodeLink->filter('.item.video-nav > .mobius > select');
                    if ($select->count() > 0) {
                        $select->filter('option')->each(function (DomCrawler $option) use (&$videoSourceUrlJson) {
                            $optionText = strtolower($option->text());
                            $optionValue = $option->attr('value');

                            // Only process english or indonesian
                            $language = null;
                            if (str_contains($optionText, 'english')) {
                                $language = 'english';
                            } elseif (str_contains($optionText, 'indonesian')) {
                                $language = 'indonesia';
                            }

                            if ($language && $optionValue) {
                                try {
                                    $decoded = base64_decode($optionValue);

                                    // Extract iframe src from decoded HTML
                                    if (preg_match('/src="([^"]+)"/', $decoded, $matches)) {
                                        $url = $matches[1];

                                        // Add https: prefix if URL starts with //
                                        if (str_starts_with($url, '//')) {
                                            $url = 'https:'.$url;
                                        }

                                        // Determine source (dailymotion or ok.ru)
                                        if (str_contains($url, 'dailymotion')) {
                                            $videoSourceUrlJson[$language]['dailymotion'] = $url;
                                        } elseif (str_contains($url, 'ok.ru')) {
                                            $videoSourceUrlJson[$language]['ok_ru'] = $url;
                                        }
                                    }
                                } catch (\Exception $e) {
                                    // Skip invalid base64
                                }
                            }
                        });
                    }

                    // Check if we have at least one valid URL
                    $hasValidUrl = ! empty($videoSourceUrlJson['english']['dailymotion'])
                        || ! empty($videoSourceUrlJson['english']['ok_ru'])
                        || ! empty($videoSourceUrlJson['indonesia']['dailymotion'])
                        || ! empty($videoSourceUrlJson['indonesia']['ok_ru']);

                    // get all episode link
                    $allEpisodes = $crawlerEpisodeLink->filter('.nvs.nvsc > a')->attr('href');

                    if ($hasValidUrl) {
                        // save episode
                        Episode::firstOrCreate(
                            ['stream_url' => $episodeLink],
                            [
                                'donghua_id' => $donghua_id,
                                'title' => trim($episodeTitle),
                                'stream_id' => $stream->id,
                                'episode_number' => $episodeNumber,
                                'video_source_url' => $videoSourceUrlJson,
                            ]
                        );

                        // update donghua -> episode_latest
                        if ($donghua->episode_latest < $episodeNumberPre) {
                            Donghua::where('id', $donghua_id)->update([
                                'episode_latest' => $episodeNumberPre,
                            ]);
                        }
                    }

                    // update donghua -> external_titles and animexin_url
                    Donghua::where('id', $donghua_id)
                        ->where(function ($query) {
                            $query->whereNull('external_titles->animexin_url')
                                ->orWhere('external_titles->animexin_url', '');
                        })
                        ->update([
                            'external_titles->animexin_url' => $allEpisodes ?? '',
                        ]);

                    // update donghua -> image_cover
                    if ($stream->is_cover_image) {
                        // get donghua image cover
                        $donghuaImage = $node->filter('.bsx > a.tip > .limit > img')->attr('src');
                        $donghuaImage = strtok($donghuaImage ?? '', '?');

                        // download image -> update donghua -> image_cover
                        $response = Http::get($donghuaImage);
                        if (! $donghua->image_cover && $response->successful()) {
                            $extension = pathinfo(parse_url($donghuaImage, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                            $fileName = 'img/covers/'.Str::uuid().'.'.$extension;
                            Storage::disk('public')->put($fileName, $response->body());

                            Donghua::where('id', $donghua_id)
                                ->update([
                                    'image_cover' => $fileName ?? '',
                                ]);
                        }
                    }
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
