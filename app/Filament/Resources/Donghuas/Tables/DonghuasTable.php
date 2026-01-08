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
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DonghuasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image_cover')
                    ->label('')
                    ->disk('public')
                    ->visibility('public')
                    ->alignCenter()
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn($record) => $record->title_en . ' Episode List')
                            ->modalDescription(fn($record) => $record->title_zh)
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->schema([
                                ViewField::make('image_preview')->view('filament.image-preview')
                                    ->viewData(fn($record) => [
                                        'image' => $record?->image_cover,
                                    ]),
                            ])
                    ),
                TextColumn::make('title_en')->label('Title (EN/CN)')->sortable()->searchable()
                    ->description(fn($record) => $record->title_zh)
                    ->width(400)
                    ->wrap(),
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
                        ToggleColumn::make('is_hot')
                            ->label('Hot')
                            ->sortable()
                            ->alignEnd(),
                        ToggleColumn::make('is_airing')
                            ->sortable()
                            ->alignEnd(),
                    ]),
            ])
            ->filters([
                // filter is_hot
                SelectFilter::make('is_hot')->label('Hot 🔥')->options(['1' => 'Yes', '0' => 'No']),
                SelectFilter::make('status_id')->label('Status')->options(Status::query()->pluck('name', 'id')),
                SelectFilter::make('is_airing')->label('Airing Status')->options(['1' => 'Airing', '0' => 'Completed']),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('watchEpisode')
                    ->label('')
                    ->color('success')
                    ->icon('heroicon-m-play')
                    ->slideOver()
                    ->modalHeading(fn($record) => "Watching: {$record->title_en}")
                    ->modalWidth('7xl')
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
                Action::make('viewDetails')
                    ->label('')
                    ->icon('heroicon-s-clipboard')
                    ->tooltip('Donghua Details')
                    ->slideOver()
                    ->modalHeading(fn($record) => $record->title_en)
                    ->modalDescription(fn($record) => $record->title_zh)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('7xl')
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
                                        TextEntry::make('is_airing')->label('Airing Status')
                                            ->badge(fn($record) => $record->is_airing ? 'primary' : 'success')
                                            ->formatStateUsing(fn($state) => $state ? 'Airing' : 'Completed')
                                            ->color(fn($state) => $state ? 'primary' : 'success'),
                                        TextEntry::make('mc_name')
                                            ->label('MC Name')
                                            ->color('primary')
                                            ->placeholder('~'),
                                        TextEntry::make('mc_wikia')->label('MC Wikia')
                                            ->url(fn($record) => $record->mc_wikia)
                                            ->openUrlInNewTab()
                                            ->color('info')
                                            ->placeholder('~'),
                                    ]),
                            ]),
                        Section::make('Description')
                            ->icon(Heroicon::InformationCircle)
                            ->schema([
                                TextEntry::make('synopsis')->color('info')->placeholder('-'),
                            ]),
                        Section::make('Episode')
                            ->icon(Heroicon::PercentBadge)
                            ->schema([
                                Grid::make()
                                    ->columns(5)
                                    ->schema([
                                        TextEntry::make('episode_watched')->label('Watched')->color('success'),
                                        TextEntry::make('episode_watched_seasonal')->label('Watched (All)')->color('success'),
                                        TextEntry::make('episode_latest')->label('Latest')->color('success'),
                                        TextEntry::make('episode_total')->label('Total')->color('success'),
                                        TextEntry::make('episode_dl')->label('Downloaded')->color('success'),
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
                EditAction::make()->label('')->tooltip('Edit'),
                DeleteAction::make()->label('')->tooltip('Delete'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'asc')
            ->recordAction(null)
            ->recordUrl(null)
            ->striped();
    }
}
