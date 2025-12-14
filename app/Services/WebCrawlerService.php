<?php
namespace App\Services;

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class WebCrawlerService
{
    /**
     * Crawl a website and return results
     */
    public function crawlWebsite(string $url, int $maxDepth = 2, int $maxUrls = 10, array $elements = []): array
    {
        $observer = new MyCrawlObserver();

        // Set custom elements to extract if provided
        if (! empty($elements)) {
            $observer->setElementsToExtract($elements);
        }

        Crawler::create()
            ->setCrawlObserver($observer)
            ->setMaximumDepth($maxDepth)
            ->setTotalCrawlLimit($maxUrls)
            ->ignoreRobots()
            ->setDelayBetweenRequests(1000)
            ->setCrawlProfile(new CrawlInternalUrls($url))
            ->startCrawling($url);

        return $observer->getResults();
    }

    /**
     * Crawl and extract specific elements using CSS selectors
     *
     * @param string $url - The URL to crawl
     * @param array $elements - CSS selectors to extract, e.g. ['h1', '.price', '#product-name']
     * @param int $maxDepth - How deep to crawl
     * @param int $maxUrls - Maximum pages to crawl
     */
    public function crawlWithElements(string $url, array $elements, int $maxDepth = 2, int $maxUrls = 10): array
    {
        return $this->crawlWebsite($url, $maxDepth, $maxUrls, $elements);
    }

    /**
     * Crawl only specific URLs (no following links)
     */
    public function crawlSpecificUrls(array $urls, array $elements = []): array
    {
        $observer = new MyCrawlObserver();

        if (! empty($elements)) {
            $observer->setElementsToExtract($elements);
        }

        foreach ($urls as $url) {
            Crawler::create()
                ->setCrawlObserver($observer)
                ->setMaximumDepth(0)
                ->startCrawling($url);
        }

        return $observer->getResults();
    }

    /**
     * Crawl with custom configuration
     */
    public function crawlWithConfig(string $url, array $config = []): array
    {
        $observer = new MyCrawlObserver();

        // Set custom elements if provided
        if (isset($config['elements']) && ! empty($config['elements'])) {
            $observer->setElementsToExtract($config['elements']);
        }

        $crawler = Crawler::create()
            ->setCrawlObserver($observer)
            ->setMaximumDepth($config['max_depth'] ?? 2)
            ->setTotalCrawlLimit($config['max_urls'] ?? 10)
            ->setDelayBetweenRequests($config['delay'] ?? 1000)
            ->setCrawlProfile(new CrawlInternalUrls($url));

        // Optional: respect robots.txt
        if (! isset($config['ignore_robots']) || ! $config['ignore_robots']) {
            // By default, it respects robots.txt
        } else {
            $crawler->ignoreRobots();
        }

        // Optional: set concurrency
        if (isset($config['concurrency'])) {
            $crawler->setConcurrency($config['concurrency']);
        }

        // Optional: set user agent
        if (isset($config['user_agent'])) {
            $crawler->setUserAgent($config['user_agent']);
        }

        $crawler->startCrawling($url);

        return $observer->getResults();
    }
}
