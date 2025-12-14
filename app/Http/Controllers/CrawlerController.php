<?php
namespace App\Http\Controllers;

use App\Services\WebCrawlerService;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    protected $crawlerService;

    public function __construct(WebCrawlerService $crawlerService)
    {
        $this->crawlerService = $crawlerService;
    }

    /**
     * Show the crawler form
     */
    public function index()
    {
        return view('crawler.index');
    }

    /**
     * Crawl a website
     */
    public function crawl(Request $request)
    {
        $request->validate([
            'url'       => 'required|url',
            'max_depth' => 'nullable|integer|min:1|max:5',
            'max_urls'  => 'nullable|integer|min:1|max:50',
            'elements'  => 'nullable|string', // Comma-separated CSS selectors
        ]);

        $url      = $request->input('url');
        $maxDepth = $request->input('max_depth', 2);
        $maxUrls  = $request->input('max_urls', 10);

        // Parse elements from comma-separated string
        $elementsString = $request->input('elements', '');
        $elements       = [];

        if (! empty($elementsString)) {
            $elements = array_map('trim', explode(',', $elementsString));
            $elements = array_filter($elements); // Remove empty values
        }

        try {
            $results = $this->crawlerService->crawlWebsite($url, $maxDepth, $maxUrls, $elements);

            return response()->json([
                'success'       => true,
                'total_crawled' => count($results),
                'results'       => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Crawling failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
