<?php
namespace App\Filament\Widgets;

use App\Filament\Resources\Donghuas\DonghuaResource;
use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\View\View;
use Filament\Tables\Enums\FiltersLayout;

class LatestEpisodes extends TableWidget
{
    use InteractsWithTable;

    protected static ?int $sort  = 2;
    public ?string $filterStatus = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Episode::query()
                    ->whereDate('created_at', '>=', now()->subDays(2))
                    ->with(['donghua', 'stream']);
            })
            ->heading('Today\'s Latest Episodes')
            ->description('This list updates automatically as the crawler finds new content.')
            ->emptyStateHeading('No new episodes yet')
            ->emptyStateDescription('Check back later or run the manual crawler.')
            ->emptyStateIcon('heroicon-o-clock')
            ->defaultSort('created_at', 'desc')
            ->columns([
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
                TextColumn::make('donghua.title_en')->searchable()->sortable()
                    ->description(function ($record) {
                        return $record->title;
                    }),
                TextColumn::make('episode_number')->label('# EP')->sortable(),
                ImageColumn::make('stream.logo')
                    ->label('Stream')
                    ->disk('public')
                    ->visibility('public')
                    ->imageHeight(28)
                    ->alignCenter()
                    ->url(fn(Episode $record): string => $record->stream_url)
                    ->openUrlInNewTab()
                    ->tooltip('Go to stream URL.'),
                TextColumn::make('created_at')
                    ->label('Added At')
                    ->dateTime('M d Y, H:i')
                    ->sortable(),
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
                        'wire:target'       => 'refresh',
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
                Action::make('editParent')
                    ->label('')
                    ->icon('heroicon-s-pencil-square')
                    ->color('warning')
                    ->url(fn($record): string => DonghuaResource::getUrl('edit', ['record' => $record->donghua_id]))
                    ->openUrlInNewTab()
                    ->tooltip('Edit Donghua'),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->extraAttributes([
                'wire:loading.class' => 'opacity-50 blur-[2px] pointer-events-none',
                'class'              => 'transition-all duration-300',
            ])
            ->poll('60s');
    }
}
