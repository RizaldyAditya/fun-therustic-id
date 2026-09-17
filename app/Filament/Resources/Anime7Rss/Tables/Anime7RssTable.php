<?php

namespace App\Filament\Resources\Anime7Rss\Tables;

use Filament\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Tables;
use Filament\Tables\Table;

class Anime7RssTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover_image_url')
                    ->label('Cover')
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->extraImgAttributes(['class' => 'max-w-full object-cover'])
                    ->action(
                        Action::make('viewCover')
                            ->modalHeading('Cover Image')
                            ->modalWidth('7xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->schema([
                                ViewField::make('image_preview')
                                    ->view('filament.image-preview')
                                    ->viewData(fn ($record) => [
                                        'image' => $record->cover_image_url,
                                    ]),
                            ])
                    ),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('post_date')
                    ->label('Post Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([25, 50, 100, 'all'])
            ->recordActions([
                Action::make('link')
                    ->icon('heroicon-m-link')
                    ->url(fn ($record): string => $record->url)
                    ->openUrlInNewTab(),

                Action::make('content')
                    ->label('Content')
                    ->icon('heroicon-m-document-text')
                    ->color('success')
                    ->modalHeading(fn ($record) => $record->title)
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->schema([
                        ViewField::make('content_preview')
                            ->view('filament.anime7-content-preview')
                            ->viewData(fn ($record) => [
                                'content' => $record->content,
                            ]),
                    ]),
            ]);
    }
}
