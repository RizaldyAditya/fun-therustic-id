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

class AnimekhorIndexObserver extends CrawlObserver
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
        $html       = (string) $response->getBody();
        $domCrawler = new DomCrawler($html);

        // get donghua title
        $donghuaTitle = $domCrawler->filter('h1.entry-title')->text();

        // find donghua id
        $donghua_id = null;
        $donghua    = Donghua::where('external_titles->animekhor_title', trim($donghuaTitle ?? ''))->first();
        if ($donghua && $donghua->id) {
            $donghua_id = $donghua->id;
        }

        // process if donghua found
        if ($donghua_id) {
            // find stream id
            $stream = Stream::where('label', 'animekhor')->first();

            // update donghua -> image_cover
            if ($stream->is_cover_image) {
                // get donghua image cover
                $donghuaImage = $domCrawler->filter('.thumbook > .thumb > img')->attr('src');
                Log::info($donghuaImage);
                $donghuaImage = strtok($donghuaImage ?? '', '?');

                // download image -> update donghua -> image_cover
                $response = Http::get($donghuaImage);
                if (!$donghua->image_cover && $response->successful()) {
                    $extension = pathinfo(parse_url($donghuaImage, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    $fileName  = 'img/covers/' . Str::uuid() . '.' . $extension;
                    Storage::disk('public')->put($fileName, $response->body());

                    Donghua::where('id', $donghua_id)
                        ->update([
                            'image_cover' => $fileName ?? '',
                        ]);
                }
            }

            $domCrawler->filter('.eplister > ul > li')->each(function (DomCrawler $node) use ($donghua_id, $stream) {
                try {
                    Sleep::for(200)->milliseconds();

                    // get episode link
                    $episodeLink = $node->filter('a')->attr('href');

                    // get episode title
                    $episodeTitle = $node->filter('a > .epl-title')->text();

                    // get episode number
                    $episodeNumberText = $node->filter('a > .epl-num')->text();
                    $parsed            = $this->sanitizeEpisodeNumber($episodeNumberText);
                    $episodeNumber     = $parsed['display'];

                    // get video source url
                    $crawlerEpisodeLink = new DomCrawler($this->client->get($episodeLink)->getBody()->getContents());
                    $iframe_div         = $crawlerEpisodeLink->filter('#pembed')->html();
                    $crawler            = new DomCrawler($iframe_div);
                    $videoSourceUrl     = $crawler->filter('iframe')->attr('src');
                    if (str_starts_with($videoSourceUrl, '//')) {
                        $videoSourceUrl = 'https:' . $videoSourceUrl;
                    }

                    if (!str_contains($videoSourceUrl, 'youtube')) {
                        // save episode
                        Episode::firstOrCreate(
                            ['stream_url' => $episodeLink],
                            [
                                'donghua_id' => $donghua_id,
                                'title' => trim($episodeTitle),
                                'stream_id' => $stream->id,
                                'episode_number' => $episodeNumber,
                                'video_source_url' => $videoSourceUrl,
                                'is_an_update' => false,
                            ]
                        );
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to parse a node: ' . $e->getMessage());
                }
            });
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
