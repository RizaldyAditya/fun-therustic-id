<?php
namespace App\Filament\Resources\Donghuas\Tables;

use App\Models\Status;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DonghuasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_cover')->disk('public')->label(''),
                TextColumn::make('id')->label('ID')->sortable()->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title_en')->label('Title')->sortable()->searchable()
                    ->description(fn($record) => $record->title_zh),
                TextColumn::make('episode_watched')->label('Watched')->sortable()
                    ->description(fn($record) => $record->episode_watched_seasonal),
                TextColumn::make('episode_latest')->label('Latest EP')->sortable(),
                SelectColumn::make('status_id')
                    ->label('Status')
                    ->options(Status::query()->pluck('name', 'id'))
                    ->searchableOptions(),
                TextColumn::make('status.name')->label('Status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('viewDetails')
                    ->label('')
                    ->icon('heroicon-o-document-text')
                    ->modalHeading(fn($record) => $record->title_en)
                    ->modalDescription(fn($record) => $record->title_zh)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('2xl')
                    ->color('info')
                    ->schema([
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                Section::make('Cover Image')
                                    ->icon(Heroicon::Photo)
                                    ->schema([
                                        ImageEntry::make('image_cover')
                                            ->hiddenLabel()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->imageHeight(300)
                                            ->alignCenter(),
                                    ]),
                                Section::make('Airing Status')
                                    ->icon(Heroicon::CalendarDays)
                                    ->schema([
                                        TextEntry::make('status.name')
                                            ->label('Status')
                                            ->badge(),
                                        TextEntry::make('airing')->label('Airing Status')
                                            ->badge(fn($record) => $record->airing ? 'primary' : 'success')
                                            ->formatStateUsing(fn($state) => $state ? 'Airing' : 'Completed'),
                                        TextEntry::make('mc_name')
                                            ->label('MC Name')
                                            ->color('primary')
                                            ->placeholder('~'),
                                        TextEntry::make('mc_wikia')->label('MC Wikia')
                                            ->url(fn($record) => $record->mc_wikia)
                                            ->openUrlInNewTab()
                                            ->color('info')
                                            ->placeholder('~')
                                            ->limit(50),
                                    ]),
                            ]),
                        Section::make('Episode')
                            ->icon(Heroicon::PercentBadge)
                            ->schema([
                                Grid::make()
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('episode_watched')->label('Watched')->color('success'),
                                        TextEntry::make('episode_watched_seasonal')->label('Watched (All)')->color('success'),
                                        TextEntry::make('episode_latest')->label('Latest')->color('success'),
                                        TextEntry::make('episode_total')->label('Total')->color('success'),
                                    ]),
                            ]),
                        Section::make('Sources')
                            ->icon(Heroicon::SquaresPlus)
                            ->schema([
                                TextEntry::make('myanimelist')
                                    ->label('MyAnimeList')
                                    ->url(fn($record) => $record->myanimelist)
                                    ->openUrlInNewTab()
                                    ->color('info')
                                    ->placeholder('-'),
                                TextEntry::make('studio.name')
                                    ->label('Studio')
                                    ->color('primary')
                                    ->placeholder('-'),
                                TextEntry::make('source.name')
                                    ->label('Source')
                                    ->color('primary')
                                    ->placeholder('-'),
                            ])
                            ->collapsed(),
                        Section::make('Data History')
                            ->icon(Heroicon::Clock)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->color('primary')
                                    ->icon(Heroicon::DocumentPlus)
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->color('primary')
                                    ->icon(Heroicon::PencilSquare)
                                    ->placeholder('-'),
                            ])
                            ->collapsed(),
                    ]),
                EditAction::make()
                    ->label(''),
                DeleteAction::make()
                    ->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])

            ->defaultSort('id', 'asc')
            ->recordUrl(null)
            ->striped();
    }
}
