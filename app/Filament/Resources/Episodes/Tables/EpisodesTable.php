<?php
namespace App\Filament\Resources\Episodes\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class EpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID')->toggleable(isToggledHiddenByDefault: false),
                ImageColumn::make('donghua.image_cover')
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
                    ),
                TextColumn::make('title')
                    ->label('Donghua')
                    ->description(function ($record) {
                        if ($record->donghua) {
                            return $record->donghua->title_en . ' (' . $record->donghua->title_zh . ')';
                        }
                        return '-';
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('episode_number')->label('# EP')->sortable()->searchable()->alignCenter(),
                ImageColumn::make('stream.logo')
                    ->label('Stream Link')
                    ->sortable()
                    ->searchable()
                    ->imageHeight(30)
                    ->alignCenter()
                    ->url(fn($record) => $record->stream_url)
                    ->openUrlInNewTab(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->isoDate('YYYY-MM-DD HH:mm')
                    ->sortable()
                    ->alignCenter(),
            ])
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
                    ->tooltip('Watch Now'),
                Action::make('copy_url')
                    ->label('')
                    ->icon('heroicon-s-clipboard-document-list')
                    ->color('primary')
                    ->tooltip('Copy video source URL to clipboard.')
                    ->action(function ($record, $livewire) {
                        $livewire->js("
                            window.navigator.clipboard.writeText('{$record->video_source_url}');
                            new FilamentNotification()
                                .title('URL copied to clipboard')
                                .success()
                                .send();
                        ");
                    }),
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
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null);
    }
}
