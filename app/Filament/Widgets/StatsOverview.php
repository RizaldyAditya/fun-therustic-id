<?php
namespace App\Filament\Widgets;

use App\Models\Donghua;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort        = 1;
    protected static bool $isLazy      = false;
    protected ?string $pollingInterval = null;
    protected ?string $heading         = 'Donghua Watch Stats';
    protected ?string $description     = "Tracking my path from a mortal viewer to a donghua sage.
        Here lies the record of every world I've visited, every series on my radar, and those still waiting for a breakthrough.
        Quality over quantity, but the stats tell the true story.";

    protected function getStats(): array
    {
        // get data counts
        $airing        = Donghua::where('airing', 1)->count();
        $watching      = Donghua::where('status_id', 3)->count();
        $plan_to_watch = Donghua::where('status_id', 2)->count();
        $completed     = Donghua::where('status_id', 5)->count();
        $on_hold       = Donghua::where('status_id', 4)->count();
        $dropped       = Donghua::where('status_id', 6)->count();

        return [
            Stat::make('Airing', $airing)
                ->color('primary')
                ->url(fn() => route('filament.admin.pages.dashboard', ['airing' => 'true'])),
            Stat::make('Watching', $watching),
            Stat::make('Plan to Watch', $plan_to_watch),
            Stat::make('Completed', $completed),
            Stat::make('On-Hold', $on_hold),
            Stat::make('Dropped', $dropped),
        ];
    }
}
