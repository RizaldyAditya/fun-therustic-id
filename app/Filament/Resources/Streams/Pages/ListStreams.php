<?php
namespace App\Filament\Resources\Streams\Pages;

use App\Filament\Resources\Streams\StreamResource;
use App\Models\Stream;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class ListStreams extends ListRecords
{
    protected static string $resource = StreamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function runCrawler(Stream $record): void
    {
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

        $stream = Stream::where('label', $record->label)->first();

        if ($stream && $stream->is_crawlable) {
            Artisan::call('app:crawl-update', [
                'website' => $stream->label,
            ]);

            Notification::make()
                ->title('Crawling completed successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Website is not crawlable.')
                ->danger()
                ->send();
        }
    }
}
