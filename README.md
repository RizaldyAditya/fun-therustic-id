# Donghua TheRustic ID — Brief Overview

This Laravel application manages a collection of "donghua" (Chinese animated series) and uses website crawlers to discover and import episode data from several external sources.

How it works
- Models: `Donghua`, `Episode`, `Stream`, `Studio`, `Source`, `Status`, `User`.
- Crawling: Uses `spatie/crawler` with custom observers under `app/Observers` to parse sites (Animexin, Animekhor, Donghuastream, Donghuaworld).
- Observers: parse list pages and episode pages, create `Episode` records, update `Donghua` metadata (latest episode, external URLs, cover images).
- UI/Admin: Filament is used for admin panel and Livewire components (e.g., an episode manager at `app/Livewire/EpisodeManager.php`).

Where crawler logic lives
- `app/Observers/*Observer.php` — per-site parsing rules and logic.
- `app/Console/Commands` — artisan commands that start crawls and wire the observers into the `spatie/crawler` runner.

Important artisan commands
- `php artisan app:crawl-test --url="https://example.com"`
  - Test a crawl against a single URL using `CrawlTest`. Useful for debugging observers.

- `php artisan app:crawl-index {website} --donghua_id={id}`
  - Runs an index-page crawl for a specific `donghua` on the given website label.
  - `website` must match a `streams.label` (e.g., `animexin`, `animekhor`, `donghuastream`, `donghuaworld`).
  - `--donghua_id` is the internal `donghua` record id to associate parsed episodes with.
  - Example: `php artisan app:crawl-index animexin --donghua_id=42`

- `php artisan app:crawl-updates {website} --page={n}`
  - Crawl the latest-updates / homepage pages for the given website label.
  - Optionally pass `--page` to crawl a specific paginated page (e.g., page 2).
  - Example: `php artisan app:crawl-updates animexin --page=2`

Notes & prerequisites
- The commands expect `streams` records (see `app/Models/Stream.php`) to indicate which websites are crawlable and their `label` and `homepage_url` values.
- Crawls create `Episode` records and may download cover images into `storage` (public disk). Ensure `storage:link` and writable storage.
- Observers skip YouTube iframe sources and only store non-YouTube video sources.

Where to look next
- Crawling logic: `app/Observers`.
- Commands: `app/Console/Commands`.
- Livewire UI: `app/Livewire/EpisodeManager.php`.

Generated summary written here for quick reference.
