<?php
namespace App\Filament\Resources\Episodes\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Contracts\View\View;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Alignment;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\ForceDeleteBulkAction;

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
                            ->schema([
                                Grid::make(1)
                                    ->extraAttributes([
                                        'class' => 'flex flex-col items-center justify-center text-center',
                                    ])
                                    ->schema([
                                        ViewField::make('image_preview')->view('filament.image-preview')
                                    ])
                            ])
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->modalAlignment(Alignment::Center)
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
                    ->label('Stream')
                    ->sortable()
                    ->searchable()
                    ->imageHeight(30)
                    ->alignCenter(),
                TextColumn::make('stream_url')
                    ->label('EP URL')
                    ->sortable()
                    ->searchable()
                    ->limit(30)
                    ->url(fn($record) => $record->stream_url)
                    ->openUrlInNewTab(),
                TextColumn::make('video_source_url')
                    ->label('Video Source URL')
                    ->sortable()
                    ->searchable()
                    ->limit(30)
                    ->url(fn($record) => $record->video_source_url)
                    ->openUrlInNewTab(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->isoDate('YYYY-MM-DD')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('viewVideoSourceUrl')
                    ->label('')
                    ->icon(Heroicon::VideoCamera)
                    ->modalHeading(fn($record) => $record->title)
                    ->modalDescription(fn($record) => $record->donghua->title_en)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->modalContent(fn($record): View => view(
                        'filament.iframe-field',
                        ['url' => $record->video_source_url]
                    ))
                    ->color('info'),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
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
