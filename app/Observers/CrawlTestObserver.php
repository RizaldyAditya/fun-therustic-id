<?php
namespace App\Observers;

use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class CrawlTestObserver extends CrawlObserver
{
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null// Ensure this is nullable string
    ): void {
        $html       = (string) $response->getBody();
        $domCrawler = new DomCrawler($html);

        // crawl full html page
        Log::info($domCrawler->html());
    }
}
