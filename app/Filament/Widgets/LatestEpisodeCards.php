<?php
namespace App\Filament\Widgets;

use App\Models\Episode;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestEpisodeCards extends TableWidget
{
    use InteractsWithTable;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Episode::query()->with('donghua', 'stream')->where('is_an_update', true)->latest())
            ->columns([
                TextColumn::make('donghua.title_en')->searchable()->extraAttributes(['style' => 'display: none;']),
                TextColumn::make('title')->searchable()->extraAttributes(['style' => 'display: none;']),
                TextColumn::make('stream.name')->searchable()->extraAttributes(['style' => 'display: none;']),
                View::make('filament.donghua-card')
            ])
            ->heading('Latest Episodes')
            ->description('Recently added episodes from all streams. Click on a card to go to the episode original page.')
            ->searchable()
            ->contentGrid([
                'default' => 1,
                'sm'      => 2,
                'md'      => 4,
                'lg'      => 5,
                'xl'      => 6,
            ])
            ->filters([
                SelectFilter::make('stream')->relationship('stream', 'name'),
            ])
            ->headerActions([
                //
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
            ->paginated([12]);
    }
}
