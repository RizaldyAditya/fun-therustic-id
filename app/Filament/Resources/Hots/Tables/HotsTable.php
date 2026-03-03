<?php
namespace App\Filament\Resources\Hots\Tables;

use App\Filament\Resources\Donghuas\DonghuaResource;
use App\Models\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image_cover')
                    ->label('')
                    ->disk('public')
                    ->visibility('public')
                    ->alignCenter()
                    ->tooltip('View Cover Image')
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
                    )
                    ->toggleable(),
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
                                'heroicon-s-check' => fn($record) => $record->episode_watched_seasonal >= $record->episode_latest,
                                'heroicon-s-eye-slash' => fn($record) => $record->episode_watched_seasonal < $record->episode_latest,
                            ])
                            ->colors([
                                'success' => fn($record) => $record->episode_watched_seasonal >= $record->episode_latest,
                                'danger' => fn($record) => $record->episode_watched_seasonal < $record->episode_latest,
                            ])
                            ->tooltip(function ($record) {
                                return $record->episode_watched_seasonal >= $record->episode_latest
                                ? 'All caught up!'
                                : ($record->episode_latest - $record->episode_watched_seasonal) . ' New episodes available to watch.';
                            }),
                        IconColumn::make('downloaded')
                            ->label('')
                            ->alignCenter()
                            ->state(static function ($record): bool {
                                return $record->episode_dl >= $record->episode_latest;
                            })
                            ->icons([
                                'heroicon-s-check' => fn($record) => $record->episode_dl >= $record->episode_latest,
                                'heroicon-s-arrow-down-on-square-stack' => fn($record) => $record->episode_dl < $record->episode_latest,
                            ])
                            ->colors([
                                'success' => fn($record) => $record->episode_dl >= $record->episode_latest,
                                'warning' => fn($record) => $record->episode_dl < $record->episode_latest,
                            ])
                            ->tooltip(function ($record) {
                                return $record->episode_dl >= $record->episode_latest
                                ? 'All latest episodes downloaded.'
                                : ($record->episode_latest - $record->episode_dl) . ' New episodes available to download.';
                            }),
                    ]),
                ColumnGroup::make('Episode')
                    ->columns([
                        TextInputColumn::make('episode_watched')
                            ->label('# Watched in Season')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->tooltip('# Watched in Season')
                            ->extraHeaderAttributes(['style' => 'width: 200px;'])
                            ->toggleable(),
                        TextInputColumn::make('episode_watched_seasonal')
                            ->label('# Watched in Total')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->tooltip('# Watched in Total')
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextInputColumn::make('episode_latest')
                            ->label('# Latest')
                            ->type('number')
                            ->alignCenter()
                            ->tooltip('# Latest')
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextInputColumn::make('episode_dl')
                            ->label('# Downloaded')
                            ->type('number')
                            ->extraInputAttributes(['step' => '1'])
                            ->alignCenter()
                            ->tooltip('# Downloaded')
                            ->extraHeaderAttributes(['style' => 'width: 200px;']),
                        TextColumn::make('episode_total')
                            ->label('# Total')
                            ->default(fn($record) => $record->episode_total ?? $record->episode_latest ?? '-')
                            ->alignCenter()
                            ->tooltip('# Total')
                            ->toggleable(),
                    ]),
                ColumnGroup::make('Status')
                    ->columns([
                        SelectColumn::make('status_id')
                            ->label('Status')
                            ->options(Status::query()->pluck('name', 'id'))
                            ->searchableOptions()
                            ->sortable()
                            ->extraHeaderAttributes(['style' => 'width: 200px; text-align: center;']),
                        ToggleColumn::make('is_hot')
                            ->label('Hot')
                            ->sortable()
                            ->alignEnd()
                            ->tooltip('Hot')
                            ->toggleable(isToggledHiddenByDefault: true),
                        ToggleColumn::make('is_airing')
                            ->label('Airing')
                            ->sortable()
                            ->alignEnd()
                            ->tooltip('Airing')
                            ->toggleable(isToggledHiddenByDefault: true),
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
                        'all' => 'All',
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
                        'wire:target' => 'refresh',
                    ]),
            ])
            ->recordActions([
                Action::make('watchEpisode')
                    ->label('')
                    ->color('success')
                    ->icon('heroicon-s-play')
                    ->slideOver()
                    ->modalHeading(fn($record) => "{$record->title_en} Episode List")
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
                                            'streamId' => 1,
                                        ]),
                                    ]),
                                Tab::make('AnimeKhor')
                                    ->schema([
                                        ViewField::make('donghua.episode_loader_ak')->view('filament.episode-loader')->viewData(fn($record) => [
                                            'donghuaId' => $record->id,
                                            'streamId' => 2,
                                        ]),
                                    ]),
                                Tab::make('DonghuaStream')
                                    ->schema([
                                        ViewField::make('donghua.episode_loader_ds')->view('filament.episode-loader')->viewData(fn($record) => [
                                            'donghuaId' => $record->id,
                                            'streamId' => 4,
                                        ]),
                                    ]),
                                Tab::make('DonghuaWorld')
                                    ->schema([
                                        ViewField::make('donghua.episode_loader_dw')->view('filament.episode-loader')->viewData(fn($record) => [
                                            'donghuaId' => $record->id,
                                            'streamId' => 5,
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->tooltip('See Episode List & Watch'),
                Action::make('myanimelist')
                    ->label('')
                    ->color('primary')
                    ->icon('icon-myanimelist')
                    ->url(fn($record) => $record->myanimelist)
                    ->openUrlInNewTab()
                    ->tooltip('MyAnimeList'),
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
                                    ->icon('heroicon-s-photo')
                                    ->schema([
                                        ImageEntry::make('image_cover')
                                            ->hiddenLabel()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->imageHeight(300)
                                            ->alignCenter(),
                                    ]),
                                Section::make('Airing Status')
                                    ->icon('heroicon-s-calendar-days')
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
                        Section::make('Episode')
                            ->icon('heroicon-s-percent-badge')
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
                            ->icon('heroicon-s-squares-plus')
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
                            ->icon('heroicon-s-clock')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->color('primary')
                                    ->icon('heroicon-s-document-plus')
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->color('primary')
                                    ->icon('heroicon-s-pencil')
                                    ->placeholder('-'),
                            ])
                            ->collapsed(),
                    ]),
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('warning')
                    ->url(fn($record): string => DonghuaResource::getUrl('edit', ['record' => $record]))
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
