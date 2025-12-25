<?php
namespace App\Observers;

use App\Models\Donghua;
use App\Models\Episode;
use App\Models\Stream;
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

class DonghuaworldObserver extends CrawlObserver
{
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

        // Your parsing logic
        $domCrawler->filter('.listupd.normal > .excstf > article')->each(function (DomCrawler $node) {
            try {
                Sleep::for(200)->milliseconds();

                // get donghua title
                $donghuaTitle = $node->filter('.bsx > a.tip > .tt')->html();
                $donghuaTitle = preg_replace('/<([a-z][a-z0-9]*)[^>]*>.*?<\/\1>/is', '', trim($donghuaTitle ?? '')); // remove html tags and its

                // get episode link
                $episodeLink = $node->filter('.bsx > a.tip')->attr('href');

                // get episode title
                $episodeTitle = $node->filter('.bsx > a.tip > .tt > h2')->text();

                // get episode number
                $node_episode = $node->filter('.bsx > a.tip > .limit > .bt > .epx');
                if ($node_episode->count() > 0) {
                    $episodeNumber = $node_episode->text();
                } else {
                    $episodeNumber = 0;
                }
                preg_match_all('/\[([^\]]*)\]/', $episodeNumber, $episodeNumberMatches);
                preg_match_all('/\d+-\d+/', $episodeNumber, $episodeNumberMatches2);
                $episodeNumber = preg_replace('/\[.*?\]/', '', $episodeNumber);
                $episodeNumber = preg_replace('/\((.*?)\)/', '', $episodeNumber);
                $episodeNumber = preg_replace('/[^0-9]/', '', $episodeNumber);
                if (!empty($episodeNumberMatches[0])) {
                    $episodeNumber .= ' ' . $episodeNumberMatches[0][0];
                }
                if (!empty($episodeNumberMatches2[0])) {
                    $episodeNumber = $episodeNumberMatches2[0][0];
                }

                // get video source url
                $crawlerEpisodeLink = new DomCrawler($this->client->get($episodeLink)->getBody()->getContents());
                $videoSourceUrl     = $crawlerEpisodeLink->filter('iframe')->attr('src');
                $allEpisodes        = $crawlerEpisodeLink->filter('.nvs.nvsc > a')->attr('href');

                if (!str_contains($videoSourceUrl, 'youtube')) {
                    // find stream id
                    $stream = Stream::where('label', 'donghuaworld')->first();

                    // find donghua id
                    $donghua_id = null;
                    $donghua    = Donghua::where('external_titles->donghuaworld_title', trim($donghuaTitle ?? ''))->first();
                    if ($donghua && $donghua->id) {
                        $donghua_id = $donghua->id;
                    }

                    // save episode
                    Episode::firstOrCreate(
                        ['stream_url' => $episodeLink],
                        [
                            'donghua_id'       => $donghua_id,
                            'title'            => trim($episodeTitle),
                            'stream_id'        => $stream->id,
                            'episode_number'   => $episodeNumber,
                            'video_source_url' => $videoSourceUrl,
                            'updated_at'       => now(),
                        ]
                    );
                }

                // update donghua -> external_titles and donghuaworld_url
                Donghua::where('id', $donghua_id)
                    ->where(function ($query) {
                        $query->whereNull('external_titles->donghuaworld_url')
                            ->orWhere('external_titles->donghuaworld_url', '');
                    })
                    ->update([
                        'external_titles->donghuaworld_url' => $allEpisodes ?? '',
                    ]);

                // update donghua -> image_cover
                if (!empty($donghua_id) && $stream->is_cover_image) {
                    // get donghua image cover
                    $donghuaImage = $node->filter('.bsx > a.tip > .limit > img')->attr('data-src');
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
            } catch (\Exception $e) {
                Log::warning('Failed to parse a node: ' . $e->getMessage());
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
