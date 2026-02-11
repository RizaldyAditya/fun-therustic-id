<?php
namespace App\Filament\Resources\Episodes\Tables;

use App\Filament\Resources\Donghuas\DonghuaResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class EpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('donghua.image_cover')
                    ->disk('public')
                    ->label('')
                    ->imageHeight(50)
                    ->alignCenter()
                    ->action(
                        Action::make('preview')
                            ->label(fn($record) => $record->donghua?->title_en)
                            ->modalHeading(fn($record) => $record->donghua?->title_en)
                            ->modalDescription(fn($record) => $record->donghua?->title_zh)
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->schema([
                                ViewField::make('image_preview')
                                    ->view('filament.image-preview')
                                    ->viewData(fn($record) => [
                                        'image' => $record->donghua?->image_cover,
                                    ]),
                            ])
                    )
                    ->toggleable(),
                TextColumn::make('title')
                    ->label('Episode Title')
                    ->description(function ($record) {
                        if ($record->donghua) {
                            return $record->donghua->title_en . ' (' . $record->donghua->title_zh . ')';
                        }
                        return '-';
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('donghua.title_en')->label('Donghua Title (en)')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('donghua.title_zh')->label('Donghua Title (zh)')->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('downloaded')
                    ->label('')
                    ->alignCenter()
                    ->state(static function ($record): bool {
                        return $record->donghua->episode_dl >= $record->donghua->episode_latest;
                    })
                    ->icons([
                        'heroicon-s-check' => fn($record) => $record->donghua->episode_dl >= $record->donghua->episode_latest,
                        'heroicon-s-arrow-down-on-square-stack' => fn($record) => $record->donghua->episode_dl < $record->donghua->episode_latest,
                    ])
                    ->colors([
                        'success' => fn($record) => $record->donghua->episode_dl >= $record->donghua->episode_latest,
                        'warning' => fn($record) => $record->donghua->episode_dl < $record->donghua->episode_latest,
                    ])
                    ->tooltip(function ($record) {
                        return $record->donghua->episode_dl >= $record->donghua->episode_latest
                        ? 'All latest episodes downloaded.'
                        : ($record->donghua->episode_latest - $record->donghua->episode_dl) . ' New episodes available to download.';
                    }),
                TextColumn::make('episode_number')->label('# Episode')->sortable()->searchable()->alignCenter()->toggleable(),
                TextColumn::make('donghua.episode_watched')
                    ->label('# Watched')
                    ->description(function ($record) {
                        if ($record->donghua) {
                            return $record->donghua->episode_watched_seasonal;
                        }
                        return '-';
                    })
                    ->alignCenter()
                    ->toggleable(),
                TextInputColumn::make('donghua.episode_dl')
                    ->label('# Downloaded')
                    ->type('number')
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->toggleable()
                    ->extraHeaderAttributes([
                        'style' => 'width: 100px',
                    ]),
                ImageColumn::make('stream.logo')
                    ->disk('public')
                    ->label('Stream Link')
                    ->sortable()
                    ->searchable()
                    ->imageHeight(30)
                    ->alignCenter()
                    ->url(fn($record) => $record->stream_url)
                    ->openUrlInNewTab(),
                TextColumn::make('video_source_url')
                    ->label('Video Source URL')
                    ->url(fn($record) => $record->video_source_url)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Release Date')
                    ->isoDate('YYYY-MM-DD HH:mm')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('stream')
                    ->relationship('stream', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->placeholder('All'),
            ])
            // ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('viewVideoSourceUrl')
                    ->label('')
                    ->icon('heroicon-s-play-circle')
                    ->modalHeading(fn($record) => $record->title)
                    ->modalDescription(fn($record) => $record->donghua->title_en)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->modalContent(fn($record): View => view(
                        'filament.iframe-field',
                        ['url' => $record->video_source_url]
                    ))
                    ->color('info')
                    ->slideover()
                    ->tooltip('Watch Now'),
                EditAction::make()->label('')->tooltip('Edit Episode'),
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('success')
                    ->url(fn($record): string => DonghuaResource::getUrl('edit', ['record' => $record->donghua_id]))
                    ->tooltip('Edit Donghua'),
                DeleteAction::make()->label('')->tooltip('Delete Episode'),
            ])
            ->toolbarActions([
                BulkAction::make('generate_json')
                    ->label('Generate JSON')
                    ->icon('heroicon-o-code-bracket')
                    ->color('success')
                    ->modalHeading('Generated Donghua & Episode JSON')
                    ->modalWidth('3xl')
                    ->slideOver()
                    ->modalSubmitAction(false)
                    ->modalContent(function (Collection $records) {
                        $grouped = $records->groupBy('donghua_id')->map(function ($episodes) {
                            $donghua = $episodes->first()->donghua; // Get the parent Donghua info

                            return [
                                'title' => $donghua->title_en,
                                'path' => $donghua->local_download_path,
                                'episodes' => $episodes->pluck('video_source_url', 'episode_number')->toArray(),
                            ];
                        })->values()->toArray();

                        return view('filament.episode-json-viewer', [
                            'json' => json_encode($grouped, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                        ]);
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([20, 30, 50]);
    }
}
