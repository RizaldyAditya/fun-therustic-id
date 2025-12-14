<?php
namespace App\Console\Commands;

use App\Services\WebCrawlerService;
use Illuminate\Console\Command;

class CrawlWebsiteCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'crawl:website
                            {url : The URL to crawl}
                            {--depth=2 : Maximum depth to crawl}
                            {--max=10 : Maximum number of URLs to crawl}
                            {--elements= : CSS selectors to extract (comma-separated)}';

    /**
     * The console command description.
     */
    protected $description = 'Crawl a website and extract custom elements';

    /**
     * Execute the console command.
     */
    public function handle(WebCrawlerService $crawlerService)
    {
        $url            = $this->argument('url');
        $depth          = $this->option('depth');
        $max            = $this->option('max');
        $elementsString = $this->option('elements');

        // Parse elements from comma-separated string
        $elements = [];
        if ($elementsString) {
            $elements = array_map('trim', explode(',', $elementsString));
            $elements = array_filter($elements);
        }

        $this->info("🕷️  Starting to crawl: {$url}");
        $this->info("📊 Max depth: {$depth}, Max URLs: {$max}");

        if (! empty($elements)) {
            $this->info("🎯 Extracting elements: " . implode(', ', $elements));
        }

        $this->newLine();

        // Show a progress bar
        $this->info('Crawling in progress...');

        $results = $crawlerService->crawlWebsite($url, $depth, $max, $elements);

        $this->newLine();
        $this->info("✅ Crawl completed! Found " . count($results) . " pages.");
        $this->newLine();

        // Display detailed results
        foreach ($results as $index => $result) {
            $this->info("─────────────────────────────────────────────────────");
            $this->info("Page " . ($index + 1) . ": " . $result['url']);
            $this->line("Status: " . $result['status']);

            if ($result['status'] !== 'failed') {
                if (! empty($result['title'])) {
                    $this->line("Title: " . $result['title']);
                }

                // Display headings
                if (! empty($result['headings'])) {
                    $h1Count = count($result['headings']['h1'] ?? []);
                    $h2Count = count($result['headings']['h2'] ?? []);
                    $h3Count = count($result['headings']['h3'] ?? []);

                    if ($h1Count > 0 || $h2Count > 0 || $h3Count > 0) {
                        $this->line("Headings: H1({$h1Count}) H2({$h2Count}) H3({$h3Count})");

                        // Show first H1 if exists
                        if ($h1Count > 0) {
                            $this->line("  H1: " . $result['headings']['h1'][0]);
                        }
                    }
                }

                // Display image count
                if (! empty($result['images'])) {
                    $this->line("Images found: " . count($result['images']));
                }

                // Display link count
                if (! empty($result['links'])) {
                    $this->line("Links found: " . count($result['links']));
                }

                // Display custom elements
                if (! empty($result['custom_elements'])) {
                    $this->line("");
                    $this->info("🎯 Custom Elements:");

                    foreach ($result['custom_elements'] as $selector => $elements) {
                        $this->line("  {$selector}: " . count($elements) . " found");

                        // Show first element content
                        if (isset($elements[0]['text']) && ! empty($elements[0]['text'])) {
                            $text = strlen($elements[0]['text']) > 80
                                ? substr($elements[0]['text'], 0, 77) . '...'
                                : $elements[0]['text'];
                            $this->line("    → " . $text);
                        }
                    }
                }
            } else {
                $this->error("  Error: " . $result['error']);
            }

            $this->newLine();
        }

        // Summary table
        $this->info("📊 Summary:");
        $tableData = array_map(function ($result) {
            return [
                'URL'    => strlen($result['url']) > 50
                    ? substr($result['url'], 0, 47) . '...'
                    : $result['url'],
                'Status' => $result['status'],
                'Title'  => isset($result['title'])
                    ? (strlen($result['title']) > 40
                        ? substr($result['title'], 0, 37) . '...'
                        : $result['title'])
                    : 'N/A',
            ];
        }, $results);

        $this->table(['URL', 'Status', 'Title'], $tableData);

        return Command::SUCCESS;
    }
}
