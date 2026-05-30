<?php

namespace App\Filament\Resources\Streams\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StreamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID')->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('logo')->disk('public'),
                TextColumn::make('homepage_url')
                    ->label('Homepage URL')
                    ->searchable()
                    ->url(fn ($record) => $record->homepage_url)
                    ->openUrlInNewTab(),
                ToggleColumn::make('is_cover_image')
                    ->label('Cover IMG')
                    ->alignEnd()
                    ->onColor('success')
                    ->offColor('primary'),
                ToggleColumn::make('is_crawlable')
                    ->label('Crawlable')
                    ->alignEnd()
                    ->onColor('success')
                    ->offColor('primary'),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->alignEnd()
                    ->onColor('success')
                    ->offColor('primary'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('runCrawler')
                    ->icon('heroicon-s-play')
                    ->label('')
                    ->color('success')
                    ->action(function ($record, $livewire) {
                        $livewire->runCrawler($record);
                    })
                    ->requiresConfirmation()
                    ->tooltip('Run Homepage Crawler for Latest Updates'),
                EditAction::make()->label('')->tooltip('Edit'),
            ])
            ->toolbarActions([])
            ->defaultSort('name', 'asc');
    }
}
