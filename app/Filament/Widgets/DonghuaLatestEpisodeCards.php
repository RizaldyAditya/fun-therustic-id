<?php

namespace App\Filament\Widgets;

use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Artisan;

class DonghuaLatestEpisodeCards extends TableWidget
{
    use InteractsWithTable;

    protected int|string|array $columnSpan = 'full';

    public function crawlAllStreams(): void
    {
        $commands = [
            'app:crawl-updates animexin',
            'app:crawl-updates donghuastream',
            'app:crawl-updates animekhor',
            'app:crawl-updates donghuaworld',
        ];

        foreach ($commands as $command) {
            Artisan::call($command);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Episode::query()->with('donghua', 'stream')
                ->where('is_an_update', true)
                ->where('created_at', '>=', now()->subDays(2))
                ->latest()
            )
            ->columns([
                TextColumn::make('donghua.title_en')->searchable()->extraAttributes(['style' => 'display: none;']),
                TextColumn::make('title')->searchable()->extraAttributes(['style' => 'display: none;']),
                TextColumn::make('stream.name')->searchable()->extraAttributes(['style' => 'display: none;']),
                View::make('filament.donghua-card'),
            ])
            ->heading('Latest Donghua Episodes')
            ->description('Recently added episodes from all streams. Click on a card to go to the episode original page.')
            ->searchable()
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 6,
                'xl' => 6,
            ])
            ->filters([
                SelectFilter::make('stream')->relationship('stream', 'name'),
            ])
            ->headerActions([
                Action::make('reloadTable')
                    ->label('Reload')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function () {
                        $this->dispatch('refresh-table');
                    }),
                Action::make('crawlLatestEpisodes')
                    ->label('Crawl for Latest Episodes')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->slideOver()
                    ->modalHeading('Manual Crawl')
                    ->modalIcon('heroicon-o-sparkles')
                    ->modalWidth('sm')
                    ->modalDescription('Would you like to do manual Crawl for Latest Episodes right now on all available stream websites?')
                    ->modalContent(view('filament.crawl-confirmation'))
                    ->modalSubmitActionLabel('Go!')
                    ->action(function () {
                        $this->crawlAllStreams();
                    }),
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultPaginationPageOption(12)
            ->poll('60s')
            ->paginated([12]);
    }
}
