<?php
namespace App\Filament\Resources\Hots\Tables;

use App\Filament\Resources\Donghuas\DonghuaResource;
use App\Models\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                ColumnGroup::make('Status')
                    ->label('')
                    ->columns([
                        IconColumn::make('watched')
                            ->label('')
                            ->alignCenter()
                            ->state(static function ($record): bool {
                                return $record->episode_watched_seasonal >= $record->episode_latest;
                            })
                            ->icons([
                                'heroicon-s-check-circle'               => fn($record)               => $record->episode_watched_seasonal >= $record->episode_latest,
                                'heroicon-s-exclamation-triangle'       => fn($record)       => $record->episode_watched_seasonal < $record->episode_latest,
                            ])
                            ->colors([
                                'success' => fn($record) => $record->episode_watched_seasonal >= $record->episode_latest,
                                'warning' => fn($record) => $record->episode_watched_seasonal < $record->episode_latest,
                            ])
                            ->tooltip(function ($record) {
                                return $record->episode_watched_seasonal >= $record->episode_latest
                                    ? 'All caught up!'
                                    : 'New episodes available to watch.';
                            }),
                        IconColumn::make('downloaded')
                            ->label('')
                            ->alignCenter()
                            ->state(static function ($record): bool {
                                return $record->episode_dl >= $record->episode_latest;
                            })
                            ->icons([
                                'heroicon-s-check-circle'               => fn($record)               => $record->episode_dl >= $record->episode_latest,
                                'heroicon-s-arrow-down-on-square-stack' => fn($record) => $record->episode_dl < $record->episode_latest,
                            ])
                            ->colors([
                                'success' => fn($record) => $record->episode_dl >= $record->episode_latest,
                                'warning' => fn($record) => $record->episode_dl < $record->episode_latest,
                            ])
                            ->tooltip(function ($record) {
                                return $record->episode_dl >= $record->episode_latest
                                    ? 'All episodes downloaded.'
                                    : 'New episodes available to download.';
                            }),
                    ]),
                ColumnGroup::make('Episode')
                    ->columns([
                        TextInputColumn::make('episode_watched')
                            ->label('# Watched in Season')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextInputColumn::make('episode_watched_seasonal')
                            ->label('# Watched in Total')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextInputColumn::make('episode_latest')
                            ->label('# Latest')
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextInputColumn::make('episode_dl')
                            ->label('# Downloaded')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextColumn::make('episode_total')
                            ->label('# Total')
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
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->options(Status::query()->pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('status')
                    ->label('Watch Status')
                    ->options([
                        'all'       => 'All',
                        'unwatched' => 'New Updates',
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
                Action::make('watchEpisode')
                    ->label('')
                    ->color('success')
                    ->icon('heroicon-s-play')
                    ->slideOver()
                    ->modalHeading(fn($record) => "Watching: {$record->title_en}")
                    ->modalWidth('full')
                    ->modalSubmitAction(false) // Hide the "Submit" button
                    ->modalCancelActionLabel('Close')
                    ->modalSubmitAction(false)
                    ->schema([
                        Tabs::make('Watch')
                            ->tabs([
                                Tab::make('AnimeXin')
                                    ->schema([
                                        ViewField::make('donghua.episode_loader_ax')->view('filament.episode-loader')->viewData(fn($record) => [
                                            'donghuaId' => $record->id,
                                            'streamId'  => 1,
                                        ]),
                                    ]),
                                Tab::make('AnimeKhor')
                                    ->schema([
                                        ViewField::make('donghua.episode_loader_ak')->view('filament.episode-loader')->viewData(fn($record) => [
                                            'donghuaId' => $record->id,
                                            'streamId'  => 2,
                                        ]),
                                    ]),
                                Tab::make('DonghuaStream')
                                    ->schema([
                                        ViewField::make('donghua.episode_loader_ds')->view('filament.episode-loader')->viewData(fn($record) => [
                                            'donghuaId' => $record->id,
                                            'streamId'  => 4,
                                        ]),
                                    ]),
                                Tab::make('DonghuaWorld')
                                    ->schema([
                                        ViewField::make('donghua.episode_loader_dw')->view('filament.episode-loader')->viewData(fn($record) => [
                                            'donghuaId' => $record->id,
                                            'streamId'  => 5,
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->tooltip('Watch Episodes'),
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('warning')
                    ->url(fn($record): string => DonghuaResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab()
                    ->tooltip('Edit Donghua'),
            ])
            ->toolbarActions([
                //
            ])
            ->paginated(false)
            ->poll('20s')
            ->defaultSort('title_en', 'asc');
    }
}
