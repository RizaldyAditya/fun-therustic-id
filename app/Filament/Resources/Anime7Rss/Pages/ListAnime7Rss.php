<?php

namespace App\Filament\Resources\Anime7Rss\Pages;

use App\Filament\Resources\Anime7Rss\Anime7RssResource;
use App\Filament\Resources\Anime7Rss\Tables\Anime7RssTable;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class ListAnime7Rss extends ListRecords
{
    protected static string $resource = Anime7RssResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crawlNow')
                ->label('Crawl Now')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Run Anime7 Crawler')
                ->modalDescription('This will fetch the latest posts from anime7.download.')
                ->modalSubmitActionLabel('Start Crawling')
                ->action(function () {
                    Artisan::call('app:crawl-anime7');

                    Notification::make()
                        ->title('Anime7 crawl completed successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return Anime7RssTable::configure($table);
    }
}
