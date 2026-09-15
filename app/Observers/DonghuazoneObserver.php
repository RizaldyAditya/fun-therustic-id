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

class DonghuazoneObserver extends CrawlObserver
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
        $domCrawler->filter('article.post-outer-container')->each(function (DomCrawler $node) {
            try {
                Sleep::for(200)->milliseconds();

                // get donghua title
                $donghuaTitle = $node->filter('.grid2-tt')->text();
                $donghuaTitle = preg_replace('/<([a-z][a-z0-9]*)[^>]*>.*?<\/\1>/is', '', trim($donghuaTitle ?? '')); // remove html tags and its

                // find donghua id
                $donghua_id = null;
                $donghua = Donghua::where('external_titles->donghuazone_title', trim($donghuaTitle ?? ''))->first();
                if ($donghua && $donghua->id) {
                    $donghua_id = $donghua->id;
                }

                if ($donghua_id) {
                    // find stream id
                    $stream = Stream::where('label', 'donghuazone')->first();

                    // get episode link
                    $episodeLink = $node->filter('a.gbox')->attr('href');

                    // get episode title
                    $episodeTitle = $node->filter('h3.post-title.entry-title a')->text();

                    // get episode number
                    $node_episode = $node->filter('.tipeps .epsid');
                    $rawText = $node_episode->count() > 0 ? $node_episode->text() : '0';
                    $parsed = $this->sanitizeEpisodeNumber($rawText);
                    $episodeNumberPre = $parsed['int'];
                    $episodeNumber = $parsed['display'];

                    // get video source url from episode page
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

                    // get video source url from server buttons (Donghuazone format)
                    $serverButtons = $crawlerEpisodeLink->filter('button.serverBtn');

                    if ($serverButtons->count() > 0) {
                        $serverButtons->each(function (DomCrawler $button) use (&$videoSourceUrlJson) {
                            $onclick = $button->attr('onclick');

                            if ($onclick && preg_match('/changeServer\(this,\s*[\'"](.+?)[\'"]\)/', $onclick, $matches)) {
                                $videoUrl = $matches[1];

                                if (! empty($videoUrl)) {
                                    // Add https: prefix if URL starts with //
                                    if (str_starts_with($videoUrl, '//')) {
                                        $videoUrl = 'https:'.$videoUrl;
                                    }

                                    // Determine source - dailymotion goes to dailymotion, all others go to ok_ru
                                    if (str_contains($videoUrl, 'dailymotion')) {
                                        $videoSourceUrlJson['english']['dailymotion'] = $videoUrl;
                                    } else {
                                        $videoSourceUrlJson['english']['ok_ru'] = $videoUrl;
                                    }
                                }
                            }
                        });
                    }

                    // Check if we have at least one valid URL
                    $hasValidUrl = ! empty($videoSourceUrlJson['english']['dailymotion'])
                        || ! empty($videoSourceUrlJson['english']['ok_ru'])
                        || ! empty($videoSourceUrlJson['indonesia']['dailymotion'])
                        || ! empty($videoSourceUrlJson['indonesia']['ok_ru']);

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

                    // update donghua -> external_titles and donghuazone_url
                    Donghua::where('id', $donghua_id)
                        ->where(function ($query) {
                            $query->whereNull('external_titles->donghuazone_url')
                                ->orWhere('external_titles->donghuazone_url', '');
                        })
                        ->update([
                            'external_titles->donghuazone_url' => $episodeLink ?? '',
                        ]);

                    // update donghua -> image_cover
                    if ($stream->is_cover_image) {
                        // get donghua image cover
                        $donghuaImage = $node->filter('img.gambar')->attr('data-src');
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
