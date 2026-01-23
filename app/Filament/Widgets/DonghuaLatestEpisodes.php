<?php
namespace App\Filament\Widgets;

use App\Models\Episode;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Widgets\TableWidget;
use Filament\Actions\DeleteAction;
use Illuminate\Contracts\View\View;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Collection;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Resources\Donghuas\DonghuaResource;
use App\Filament\Resources\Episodes\Schemas\EpisodesForm;

class DonghuaLatestEpisodes extends TableWidget
{
    use InteractsWithTable;

    protected static ?int $sort                = 2;
    public ?string $filterStatus               = null;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Episode::query()->with(['donghua', 'stream']);
            })
            ->heading('')
            ->description('This list updates automatically as the crawler finds new content.')
            ->emptyStateHeading('No new episodes yet')
            ->emptyStateDescription('Check back later or run the manual crawler.')
            ->emptyStateIcon('heroicon-o-clock')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('donghua.image_cover')
                    ->label('')
                    ->disk('public')
                    ->visibility('public')
                    ->alignCenter()
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn($record) => $record->donghua->title_en)
                            ->modalDescription(fn($record) => $record->donghua->title_zh)
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->schema([
                                ViewField::make('donghua.image_preview')->view('filament.image-preview')
                                    ->viewData(fn($record) => [
                                        'image' => $record->donghua?->image_cover,
                                    ]),
                            ])
                    ),
                TextColumn::make('donghua.title_en')
                    ->label('Title')
                    ->description(function ($record) {
                        return $record->title;
                    })
                    ->searchable()
                    ->sortable(),
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
                        return ($record->donghua->episode_dl >= $record->donghua->episode_latest)
                        ? 'All latest episodes downloaded.'
                        : ($record->donghua->episode_latest - $record->donghua->episode_dl) . ' New episodes available to download.';
                    }),
                TextColumn::make('episode_number')->label('# EP')->sortable(),
                TextInputColumn::make('donghua.episode_dl')
                    ->label('# Downloaded')
                    ->type('number')
                    ->sortable()
                    ->alignCenter()
                    ->width(100),
                ImageColumn::make('stream.logo')
                    ->label('Stream')
                    ->disk('public')
                    ->visibility('public')
                    ->imageHeight(28)
                    ->alignCenter()
                    ->url(fn(Episode $record): string => $record->stream_url)
                    ->openUrlInNewTab()
                    ->tooltip('Go to stream URL.'),
                TextColumn::make('stream_url')
                    ->label('Stream URL')
                    ->url(fn(Episode $record): string => $record->stream_url)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('video_source_url')
                    ->label('Video Source URL')
                    ->url(fn(Episode $record): string => $record->video_source_url)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Added At')
                    ->dateTime('M d Y, H:i')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('created_at', $direction);
                    }),
            ])
            ->filters([
                SelectFilter::make('stream_id')
                    ->label('Stream')
                    ->relationship('stream', 'name')
                    ->placeholder('All Streams'),
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('refresh')
                    ->label('Reload')
                    ->icon('heroicon-m-arrow-path')
                    ->color('primary')
                    ->action(fn() => $this->dispatch('$refresh'))
                    ->extraAttributes([
                        'wire:loading.attr' => 'disabled',
                        'wire:target' => 'refresh',
                    ]),
            ])
            ->recordActions([
                Action::make('viewVideoSourceUrl')
                    ->label('')
                    ->icon('heroicon-s-play-circle')
                    ->slideOver()
                    ->modalHeading(fn($record) => $record->title)
                    ->modalDescription(fn($record) => $record->donghua->title_en)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->modalContent(fn($record): View => view(
                        'filament.iframe-field',
                        ['url' => $record->video_source_url]
                    ))
                    ->color('success')
                    ->tooltip('Watch Now'),
                Action::make('viewDetails')
                    ->label('')
                    ->icon('heroicon-s-document-magnifying-glass')
                    ->slideOver()
                    ->tooltip('Donghua Details')
                    ->modalHeading(fn($record) => $record->donghua->title_en)
                    ->modalDescription(fn($record) => $record->donghua->title_zh)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->color('info')
                    ->schema([
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                Section::make('Cover Image')
                                    ->icon(Heroicon::Photo)
                                    ->schema([
                                        ImageEntry::make('donghua.image_cover')
                                            ->hiddenLabel()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->imageHeight(300)
                                            ->alignCenter(),
                                    ]),
                                Section::make('Airing Status')
                                    ->icon(Heroicon::CalendarDays)
                                    ->schema([
                                        TextEntry::make('donghua.status.name')
                                            ->label('Status')
                                            ->badge(),
                                        TextEntry::make('donghua.is_airing')->label('Airing Status')
                                            ->badge(fn($record) => $record->donghua->is_airing ? 'primary' : 'success')
                                            ->formatStateUsing(fn($state) => $state ? 'Airing' : 'Completed')
                                            ->color(fn($state) => $state ? 'primary' : 'success'),
                                        TextEntry::make('donghua.mc_name')
                                            ->label('MC Name')
                                            ->color('primary')
                                            ->placeholder('~'),
                                        TextEntry::make('donghua.mc_wikia')->label('MC Wikia')
                                            ->url(fn($record) => $record->donghua->mc_wikia)
                                            ->openUrlInNewTab()
                                            ->color('info')
                                            ->placeholder('~'),
                                    ]),
                            ]),
                        Section::make('Episode')
                            ->icon(Heroicon::PercentBadge)
                            ->schema([
                                Grid::make()
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('donghua.episode_watched')->label('Watched')->color('success'),
                                        TextEntry::make('donghua.episode_watched_seasonal')->label('Watched (All)')->color('success'),
                                        TextEntry::make('donghua.episode_latest')->label('Latest')->color('success'),
                                        TextEntry::make('donghua.episode_total')->label('Total')->color('success'),
                                    ]),
                            ]),
                        Section::make('Sources')
                            ->icon(Heroicon::SquaresPlus)
                            ->schema([
                                TextEntry::make('donghua.myanimelist')
                                    ->label('MyAnimeList')
                                    ->url(fn($record) => $record->donghua->myanimelist)
                                    ->openUrlInNewTab()
                                    ->color('info')
                                    ->placeholder('-'),
                                TextEntry::make('donghua.studio.name')
                                    ->label('Studio')
                                    ->color('primary')
                                    ->placeholder('-'),
                                TextEntry::make('donghua.source.name')
                                    ->label('Source')
                                    ->color('primary')
                                    ->placeholder('-'),
                            ])
                            ->collapsed(),
                        Section::make('Data History')
                            ->icon(Heroicon::Clock)
                            ->schema([
                                TextEntry::make('donghua.created_at')
                                    ->dateTime()
                                    ->color('primary')
                                    ->icon(Heroicon::DocumentPlus)
                                    ->placeholder('-'),
                                TextEntry::make('donghua.updated_at')
                                    ->dateTime()
                                    ->color('primary')
                                    ->icon(Heroicon::PencilSquare)
                                    ->placeholder('-'),
                            ])
                            ->collapsed(),
                    ]),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Episode')
                    ->slideOver()
                    ->modalHeading('Edit Episode')
                    ->modalWidth('5xl')
                    ->color('warning')
                    ->schema([
                        TextInput::make('title')->required()->inlineLabel(),
                        Select::make('donghua_id')
                            ->relationship('donghua', 'title_en')
                            ->required()
                            ->inlineLabel()
                            ->searchable()
                            ->preload(false),
                        TextInput::make('episode_number')->numeric()->required()->inlineLabel(),
                        Select::make('stream_id')
                            ->relationship('stream', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->inlineLabel(),
                        TextInput::make('stream_url')->required()->inlineLabel(),
                        TextInput::make('video_source_url')->required()->inlineLabel(),
                        Textarea::make('notes')->rows(4),
                    ]),
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('success')
                    ->url(fn($record): string => DonghuaResource::getUrl('edit', ['record' => $record->donghua_id]))
                    ->openUrlInNewTab()
                    ->slideOver()
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
            ])
            ->extraAttributes([
                'wire:loading.class' => 'opacity-50 blur-[2px] pointer-events-none',
                'class' => 'transition-all duration-300',
            ])
            ->recordUrl(false)
            ->poll('60s');
    }
}
