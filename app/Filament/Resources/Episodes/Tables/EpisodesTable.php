<?php

namespace App\Filament\Resources\Episodes\Tables;

use App\Filament\Resources\Donghuas\DonghuaResource;
use App\Models\Stream;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\ViewField;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
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
                            ->label(fn ($record) => $record->donghua?->title_en)
                            ->modalHeading(fn ($record) => $record->donghua?->title_en)
                            ->modalDescription(fn ($record) => $record->donghua?->title_zh)
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->schema([
                                ViewField::make('image_preview')
                                    ->view('filament.image-preview')
                                    ->viewData(fn ($record) => [
                                        'image' => $record->donghua?->image_cover,
                                    ]),
                            ])
                    )
                    ->toggleable(),
                TextColumn::make('title')
                    ->label('Episode Title')
                    ->description(function ($record) {
                        if ($record->donghua) {
                            return $record->donghua->title_en.' ('.$record->donghua->title_zh.')';
                        }

                        return '-';
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('donghua.title_en')->label('Donghua Title (en)')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('donghua.title_zh')->label('Donghua Title (zh)')->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('primary_stream')
                    ->label('')
                    ->alignCenter()
                    ->state(static function ($record): bool {
                        return !empty($record->donghua->primary_stream->label) && $record->donghua->primary_stream->label === $record->stream->label;
                    })
                    ->icons([
                        'heroicon-s-clipboard-document-check' => fn ($record) => !empty($record->donghua->primary_stream->label) && $record->donghua->primary_stream->label === $record->stream->label,
                        'heroicon-s-bookmark-slash' => fn ($record) => empty($record->donghua->primary_stream->label) || $record->donghua->primary_stream->label !== $record->stream->label,
                    ])
                    ->colors([
                        'success' => fn ($record) => !empty($record->donghua->primary_stream->label) && $record->donghua->primary_stream->label === $record->stream->label,
                        'danger' => fn ($record) => empty($record->donghua->primary_stream->label) || $record->donghua->primary_stream->label !== $record->stream->label,
                    ])
                    ->tooltip(function ($record) {
                        return !empty($record->donghua->primary_stream->label) && $record->donghua->primary_stream->label === $record->stream->label
                        ? 'This is the primary stream.'
                        : 'This is not the primary stream.';
                    }),
                IconColumn::make('downloaded')
                    ->label('')
                    ->alignCenter()
                    ->state(static function ($record): bool {
                        return $record->donghua->episode_dl >= $record->donghua->episode_latest;
                    })
                    ->icons([
                        'heroicon-s-check' => fn ($record) => $record->donghua->episode_dl >= $record->donghua->episode_latest,
                        'heroicon-s-arrow-down-on-square-stack' => fn ($record) => $record->donghua->episode_dl < $record->donghua->episode_latest,
                    ])
                    ->colors([
                        'success' => fn ($record) => $record->donghua->episode_dl >= $record->donghua->episode_latest,
                        'warning' => fn ($record) => $record->donghua->episode_dl < $record->donghua->episode_latest,
                    ])
                    ->tooltip(function ($record) {
                        return $record->donghua->episode_dl >= $record->donghua->episode_latest
                        ? 'All latest episodes downloaded.'
                        : ($record->donghua->episode_latest - $record->donghua->episode_dl).' New episodes available to download.';
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
                    ->url(fn ($record) => $record->stream_url)
                    ->openUrlInNewTab(),
                TextColumn::make('video_source_url')
                    ->label('Video Source URL')
                    ->formatStateUsing(function ($record) {
                        $urls = $record->video_source_url;
                        if (! $urls) {
                            return null;
                        }

                        // Prefer English dailymotion, then English ok.ru
                        return $urls['english']['dailymotion'] ?? $urls['english']['ok_ru'] ?? null;
                    })
                    ->url(function ($record) {
                        $urls = $record->video_source_url;
                        if (! $urls) {
                            return null;
                        }

                        return $urls['english']['dailymotion'] ?? $urls['english']['ok_ru'] ?? null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                SelectColumn::make('donghua.dl_stream')
                    ->label('DL Stream')
                    ->options(Stream::query()->pluck('name', 'label'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->native(false),
                TextColumn::make('created_at')
                    ->label('Release Date')
                    ->date('F jS, Y')
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
                    ->modalHeading(fn ($record) => $record->title)
                    ->modalDescription(fn ($record) => $record->donghua->title_en)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->modalContent(fn ($record): View => view(
                        'filament.iframe-field',
                        ['url' => $record->video_source_url['english']['dailymotion'] ?? $record->video_source_url['english']['ok_ru'] ?? null]
                    ))
                    ->color('info')
                    ->slideover()
                    ->tooltip('Watch Now'),
                Action::make('myanimelist')
                    ->label('')
                    ->color('primary')
                    ->icon('icon-myanimelist')
                    ->url(fn ($record) => $record->donghua->myanimelist)
                    ->openUrlInNewTab()
                    ->tooltip('MyAnimeList'),
                EditAction::make()->label('')->tooltip('Edit Episode'),
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('success')
                    ->url(fn ($record): string => DonghuaResource::getUrl('edit', ['record' => $record->donghua_id]))
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
                            $donghua = $episodes->first()->donghua;

                            $episodeUrls = [];
                            foreach ($episodes as $episode) {
                                $urls = $episode->video_source_url;
                                $episodeUrls[$episode->episode_number] = [];

                                if (! empty($urls['english']['dailymotion'])) {
                                    $episodeUrls[$episode->episode_number][] = $urls['english']['dailymotion'];
                                }
                                if (! empty($urls['english']['ok_ru'])) {
                                    $episodeUrls[$episode->episode_number][] = $urls['english']['ok_ru'];
                                }
                            }

                            return [
                                'title' => $donghua->title_en,
                                'path' => $donghua->local_download_path,
                                'episodes' => $episodeUrls,
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
