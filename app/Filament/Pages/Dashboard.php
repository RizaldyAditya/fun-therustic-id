<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use App\Filament\Widgets\DonghuaStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DonghuaLatestEpisodes;
use App\Filament\Widgets\LatestEpisodesSwitcher;
use App\Filament\Widgets\DonghuaLatestEpisodeCards;

class Dashboard extends BaseDashboard
{
    public string $activeTab = 'donghua';
    public string $latestViewType = 'card';

    protected function getQueryParams(): array
    {
        return ['tab' => $this->activeTab];
    }

    public function getWidgets(): array
    {
        if ($this->activeTab === 'anime') {
            return [

            ];
        }

        return [
            $this->latestViewType === 'card' ? DonghuaLatestEpisodeCards::class : DonghuaLatestEpisodes::class,
            DonghuaStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('setDonghua')
                ->label('Donghua RSS')
                ->color(fn() => $this->activeTab === 'donghua' ? 'success' : 'gray')
                ->icon('heroicon-m-sparkles')
                ->action(function () {
                    $this->activeTab = 'donghua';
                }),

            Action::make('setAnime')
                ->label('Anime RSS')
                ->color(fn() => $this->activeTab === 'anime' ? 'success' : 'gray')
                ->icon('heroicon-m-bolt')
                ->action(function () {
                    $this->activeTab = 'anime';
                }),

            Action::make('toggleView')
                ->label('')
                ->color('info')
                ->icon(fn() => $this->latestViewType === 'card' ? 'heroicon-m-table-cells' : 'heroicon-m-squares-2x2')
                ->action(function () {
                    $this->latestViewType = $this->latestViewType === 'card' ? 'table' : 'card';
                }),
        ];
    }

    public function getColumns(): int | array
    {
        return $this->activeTab === 'anime' ? 2 : 1;
    }
}
