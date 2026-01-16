<?php

namespace App\Filament\Resources\AnimeEpisodes\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Contracts\View\View;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\Animes\AnimeResource;

class AnimeEpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                ImageColumn::make('anime.poster')
                    ->label('')
                    ->disk('public')
                    ->alignCenter()
                    ->toggleable()
                    ->visibility('public')
                    ->alignCenter()
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn($record) => $record->title . ' Episode List')
                            ->modalDescription(fn($record) => $record->title_jp)
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->schema([
                                ViewField::make('image_preview')->view('filament.image-preview')
                                    ->viewData(fn($record) => [
                                        'image' => $record?->anime?->poster,
                                    ]),
                            ])
                    ),
                TextColumn::make('anime.title')
                    ->label('Anime Title')
                    ->description(fn($record) => $record->anime->title_jp),
                TextColumn::make('title')
                    ->label('Episode Title')
                    ->sortable()
                    ->searchable(),
                TextInputColumn::make('episode_number')
                    ->label('# Episode')
                    ->type('number')
                    ->width(100)
                    ->alignCenter(),
                TextColumn::make('notes')
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('stream.logo')
                    ->disk('public')
                    ->alignCenter(),
                SelectColumn::make('subtitle_lang')
                    ->label('Sub Language')
                    ->sortable()
                    ->options([
                        'en' => 'English',
                        'id' => 'Indonesia',
                        'und' => 'Unspecified',
                    ])
                    ->width(100),
                ToggleColumn::make('is_active')
                    ->label('Status')
                    ->sortable()
                    ->alignCenter()
                    ->width(100)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('stream_id')
                    ->label('Stream')
                    ->relationship('stream', 'name')
                    ->multiple()
                    ->placeholder('All'),
                SelectFilter::make('subtitle_lang')
                    ->label('Sub Language')
                    ->options([
                        'en' => 'English',
                        'id' => 'Indonesia',
                        'und' => 'Unspecified',
                    ]),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('viewVideoSourceUrl')
                    ->label('')
                    ->icon('heroicon-s-play-circle')
                    ->modalHeading(fn($record) => $record->title)
                    ->modalDescription(fn($record) => $record->anime->title_jp)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->modalContent(fn($record): View => view(
                        'filament.iframe-field',
                        ['url' => $record->video_url]
                    ))
                    ->color('info')
                    ->slideover()
                    ->tooltip('Watch Now'),
                Action::make('openStreamWebsiteInNewTab')
                    ->label('')
                    ->icon('heroicon-s-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn($record) => $record->stream_url)
                    ->openUrlInNewTab()
                    ->tooltip('Open Stream URL in New Tab'),
                EditAction::make()->label('')->tooltip('Edit Anime Episode'),
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('success')
                    ->url(fn($record): string => AnimeResource::getUrl('edit', ['record' => $record->anime_id]))
                    ->tooltip('Edit Anime'),
                DeleteAction::make()->label('')->tooltip('Delete Anime Episode'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
