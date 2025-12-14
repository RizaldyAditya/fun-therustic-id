<?php
namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class MyCrawlObserver extends CrawlObserver
{
    protected $results           = [];
    protected $elementsToExtract = [];

    /**
     * Set which elements to extract
     *
     * @param array $elements - Array of CSS selectors to extract
     * Example: ['h1', 'h2', '.product-price', '#main-content']
     */
    public function setElementsToExtract(array $elements): void
    {
        $this->elementsToExtract = $elements;
    }

    /**
     * Called when a page was crawled successfully
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null
    ): void {
        $statusCode = $response->getStatusCode();
        $html       = (string) $response->getBody();

        // Parse HTML using Symfony DomCrawler
        $domCrawler = new DomCrawler($html);

        // Extract page title
        $title = $this->extractElement($domCrawler, 'title');

        // Extract meta description
        $description = $this->extractMetaContent($domCrawler, 'description');

        // Extract meta keywords
        $keywords = $this->extractMetaContent($domCrawler, 'keywords');

        // Extract all headings
        $headings = $this->extractAllHeadings($domCrawler);

        // Extract all images
        $images = $this->extractAllImages($domCrawler, $url);

        // Extract all links
        $links = $this->extractAllLinks($domCrawler, $url);

        // Extract custom elements if specified
        $customElements = [];
        if (! empty($this->elementsToExtract)) {
            $customElements = $this->extractCustomElements($domCrawler);
        }

        // Store the results
        $this->results[] = [
            'url'             => (string) $url,
            'status'          => $statusCode,
            'title'           => $title,
            'description'     => $description,
            'keywords'        => $keywords,
            'headings'        => $headings,
            'images'          => $images,
            'links'           => $links,
            'custom_elements' => $customElements,
            'link_text'       => $linkText,
            'found_on'        => $foundOnUrl ? (string) $foundOnUrl : 'Starting URL',
            'crawled_at'      => now()->toDateTimeString(),
        ];

        Log::info("Crawled: {$url} - Status: {$statusCode} - Title: {$title}");
    }

    /**
     * Extract a single element
     */
    protected function extractElement(DomCrawler $crawler, string $selector, string $default = ''): string
    {
        try {
            $element = $crawler->filter($selector);
            return $element->count() > 0 ? trim($element->text()) : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Extract meta tag content
     */
    protected function extractMetaContent(DomCrawler $crawler, string $name): string
    {
        try {
            $meta = $crawler->filter("meta[name=\"{$name}\"]");
            if ($meta->count() > 0) {
                return trim($meta->attr('content'));
            }
        } catch (\Exception $e) {
            // Do nothing
        }
        return '';
    }

    /**
     * Extract all headings (h1-h6)
     */
    protected function extractAllHeadings(DomCrawler $crawler): array
    {
        $headings = [
            'h1' => [],
            'h2' => [],
            'h3' => [],
            'h4' => [],
            'h5' => [],
            'h6' => [],
        ];

        foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
            try {
                $crawler->filter($tag)->each(function (DomCrawler $node) use (&$headings, $tag) {
                    $text = trim($node->text());
                    if (! empty($text)) {
                        $headings[$tag][] = $text;
                    }
                });
            } catch (\Exception $e) {
                // Continue if error
            }
        }

        return $headings;
    }

    /**
     * Extract all images with their attributes
     */
    protected function extractAllImages(DomCrawler $crawler, UriInterface $baseUrl): array
    {
        $images = [];

        try {
            $crawler->filter('img')->each(function (DomCrawler $node) use (&$images, $baseUrl) {
                $src = $node->attr('src');
                $alt = $node->attr('alt') ?? '';

                // Convert relative URLs to absolute
                if ($src && ! filter_var($src, FILTER_VALIDATE_URL)) {
                    $src = rtrim((string) $baseUrl, '/') . '/' . ltrim($src, '/');
                }

                if ($src) {
                    $images[] = [
                        'src'   => $src,
                        'alt'   => $alt,
                        'title' => $node->attr('title') ?? '',
                    ];
                }
            });
        } catch (\Exception $e) {
            // Continue if error
        }

        return $images;
    }

    /**
     * Extract all links
     */
    protected function extractAllLinks(DomCrawler $crawler, UriInterface $baseUrl): array
    {
        $links = [];

        try {
            $crawler->filter('a')->each(function (DomCrawler $node) use (&$links, $baseUrl) {
                $href = $node->attr('href');
                $text = trim($node->text());

                // Convert relative URLs to absolute
                if ($href && ! filter_var($href, FILTER_VALIDATE_URL)) {
                    if (strpos($href, '#') === 0 || strpos($href, 'javascript:') === 0) {
                        return; // Skip anchor links and javascript
                    }
                    $href = rtrim((string) $baseUrl, '/') . '/' . ltrim($href, '/');
                }

                if ($href) {
                    $links[] = [
                        'href' => $href,
                        'text' => $text,
                        'rel'  => $node->attr('rel') ?? '',
                    ];
                }
            });
        } catch (\Exception $e) {
            // Continue if error
        }

        return $links;
    }

    /**
     * Extract custom elements based on CSS selectors
     */
    protected function extractCustomElements(DomCrawler $crawler): array
    {
        $extracted = [];

        foreach ($this->elementsToExtract as $selector) {
            try {
                $elements = [];

                $crawler->filter($selector)->each(function (DomCrawler $node) use (&$elements, $selector) {
                    // Try to get text content
                    $text = trim($node->text());

                    // Also get HTML if it's a complex element
                    $html = trim($node->html());

                    // Get all attributes
                    $attributes = [];
                    if ($node->getNode(0)) {
                        foreach ($node->getNode(0)->attributes as $attr) {
                            $attributes[$attr->name] = $attr->value;
                        }
                    }

                    $elements[] = [
                        'text'       => $text,
                        'html'       => strlen($html) > 500 ? substr($html, 0, 500) . '...' : $html,
                        'attributes' => $attributes,
                    ];
                });

                if (! empty($elements)) {
                    $extracted[$selector] = $elements;
                }
            } catch (\Exception $e) {
                $extracted[$selector] = ['error' => $e->getMessage()];
            }
        }

        return $extracted;
    }

    /**
     * Called when the crawler had a problem crawling the given url
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null
    ): void {
        Log::error("Failed to crawl: {$url}");

        $this->results[] = [
            'url'       => (string) $url,
            'status'    => 'failed',
            'error'     => $requestException->getMessage(),
            'link_text' => $linkText,
            'found_on'  => $foundOnUrl ? (string) $foundOnUrl : 'Starting URL',
        ];
    }

    /**
     * Get all crawled results
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
