<?php
namespace App\Filament\Resources\Hots\Tables;

use App\Models\Status;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\Donghuas\DonghuaResource;

class HotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_cover')
                    ->label('')
                    ->disk('public')
                    ->visibility('public')
                    ->alignCenter()
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn($record) => $record->title_en)
                            ->modalDescription(fn($record) => $record->title_zh)
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->schema([
                                ViewField::make('donghua.image_preview')->view('filament.image-preview')
                                    ->viewData(fn($record) => [
                                        'image' => $record->image_cover,
                                    ]),
                            ])
                    ),
                TextColumn::make('title_en')
                    ->label('Title')
                    ->description(function ($record) {
                        return $record->title_zh;
                    })
                    ->searchable()
                    ->sortable(),
                IconColumn::make('watched')
                    ->label('Status')
                    ->alignCenter()
                    ->state(static function ($record): bool {
                        // This ensures the column has a value to work with
                        return $record->episode_watched_seasonal >= $record->episode_latest;
                    })
                    ->icons([
                        'heroicon-s-check-circle'         => fn($record)         => $record->episode_watched_seasonal >= $record->episode_latest,
                        'heroicon-s-exclamation-triangle' => fn($record) => $record->episode_watched_seasonal < $record->episode_latest,
                    ])
                    ->colors([
                        'success' => fn($record) => $record->episode_watched_seasonal >= $record->episode_latest,
                        'warning' => fn($record) => $record->episode_watched_seasonal < $record->episode_latest,
                    ])
                    ->tooltip(function ($record) {
                        return $record->episode_watched_seasonal < $record->episode_latest
                            ? 'New episodes available!'
                            : 'Up to date';
                    }),
                ColumnGroup::make('Watched Episode')
                    ->columns([
                        TextInputColumn::make('episode_watched')
                            ->label('Season')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextInputColumn::make('episode_watched_seasonal')
                            ->label('Seasonal')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextInputColumn::make('episode_latest')
                            ->label('Latest')
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextColumn::make('episode_total')
                            ->label('Total')
                            ->default(fn($record) => $record->episode_total ?? $record->episode_latest ?? '-')
                            ->alignCenter(),
                    ]),
                ColumnGroup::make('Status')
                    ->columns([
                        SelectColumn::make('status_id')
                            ->label('Status')
                            ->options(Status::query()->pluck('name', 'id'))
                            ->searchableOptions()
                            ->extraHeaderAttributes(['style' => 'width: 200px; text-align: center;']),
                        ToggleColumn::make('is_observed')
                            ->label('Hot')
                            ->sortable()
                            ->alignEnd(),
                        ToggleColumn::make('airing')
                            ->sortable()
                            ->alignEnd(),
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Watch Status')
                    ->options([
                        'all'       => 'All',
                        'unwatched' => 'Unwatched',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'unwatched',
                            fn(Builder $query): Builder => $query->whereColumn('episode_watched_seasonal', '<', 'episode_latest')
                                ->where('episode_watched_seasonal', '>', 0)
                                ->where('episode_latest', '>', 0),
                        );
                    }),
            ])
            ->headerActions([
                Action::make('refresh')
                    ->label('Reload')
                    ->icon('heroicon-m-arrow-path')
                    ->color('primary')
                    ->action(fn($livewire) => $livewire->dispatch('$refresh'))
                    ->extraAttributes([
                        'wire:loading.attr' => 'disabled',
                        'wire:target'       => 'refresh',
                    ]),
            ])
            ->recordActions([
                Action::make('watchEpisodeAx')
                    ->label('AX')
                    ->color('success')
                    ->icon('heroicon-m-play-circle')
                    ->slideOver()
                    ->modalHeading(fn($record) => "Watching: {$record->title_en}")
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false) // Hide the "Submit" button
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn($record) => view('filament.episode-loader', [
                        'donghuaId' => $record->id,
                        'streamId'  => 1,
                    ]))
                    ->modalSubmitAction(false)
                    ->tooltip('Watch from AnimeXin'),
                Action::make('watchEpisodeAk')
                    ->label('AK')
                    ->color('info')
                    ->icon('heroicon-m-play-circle')
                    ->slideOver()
                    ->modalHeading(fn($record) => "Watching: {$record->title_en}")
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false) // Hide the "Submit" button
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn($record) => view('filament.episode-loader', [
                        'donghuaId' => $record->id,
                        'streamId'  => 2,
                    ]))
                    ->modalSubmitAction(false)
                    ->tooltip('Watch from AnimeKhor'),
                Action::make('watchEpisodeDs')
                    ->label('DH')
                    ->color('danger')
                    ->icon('heroicon-m-play-circle')
                    ->slideOver()
                    ->modalHeading(fn($record) => "Watching: {$record->title_en}")
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false) // Hide the "Submit" button
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn($record) => view('filament.episode-loader', [
                        'donghuaId' => $record->id,
                        'streamId'  => 4,
                    ]))
                    ->modalSubmitAction(false)
                    ->tooltip('Watch from DonghuaStream'),
                Action::make('watchEpisodeDw')
                    ->label('DW')
                    ->color('warning')
                    ->icon('heroicon-m-play-circle')
                    ->slideOver()
                    ->modalHeading(fn($record) => "Watching: {$record->title_en}")
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false) // Hide the "Submit" button
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn($record) => view('filament.episode-loader', [
                        'donghuaId' => $record->id,
                        'streamId'  => 5,
                    ]))
                    ->modalSubmitAction(false)
                    ->tooltip('Watch from DonghuaWorld'),
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('warning')
                    ->url(fn($record): string => DonghuaResource::getUrl('edit', ['record' => $record->id]))
                    ->tooltip('Edit Donghua'),
            ])
            ->toolbarActions([
                //
            ])
            ->paginated(false)
            ->poll('20s');
    }
}
