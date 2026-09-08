<?php

namespace App\Filament\Resources\SocigamesRss\Tables;

use App\Models\Avn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class SocigamesRssTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->disk('public')
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->extraImgAttributes(['class' => 'max-w-full object-cover'])
                    ->action(
                        Action::make('viewCover')
                            ->modalHeading('Cover Image')
                            ->modalWidth('7xl')
                            ->modalContent(fn ($record) => new HtmlString(
                                '<img src="'.e(Str::startsWith($record->cover_image, ['http://', 'https://']) ? $record->cover_image : asset('storage/'.$record->cover_image)).'" style="width:100%; height:auto;" />'
                            ))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                    ),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('version')
                    ->label('Version')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('release_date')
                    ->label('Release Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('release_date', 'desc')
            ->paginated([25, 50, 100, 'all'])
            ->recordActions([
                Action::make('link')
                    ->icon('heroicon-m-link')
                    ->url(fn ($record): string => $record->url)
                    ->openUrlInNewTab(),

                Action::make('get')
                    ->label('Get')
                    ->color('success')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->modalHeading('Import to AVNs')
                    ->modalWidth('lg')
                    ->modalContent(fn ($record) => new HtmlString(
                        '<div class="space-y-3">'
                            .'<p>Import this entry into your AVN collection?</p>'
                            .'<div class="rounded-lg border p-4 space-y-2">'
                                .'<p><strong>Title:</strong> '.e($record->title).'</p>'
                                .'<p><strong>Version:</strong> '.e($record->version ?? 'N/A').'</p>'
                                .'<p><strong>Release Date:</strong> '.e($record->release_date?->format('M d, Y') ?? 'N/A').'</p>'
                            .'</div>'
                        .'</div>'
                    ))
                    ->modalSubmitActionLabel('Import')
                    ->action(function ($record) {
                        $exists = Avn::where('title', $record->title)->exists();

                        if ($exists) {
                            Notification::make()
                                ->title('Already exists')
                                ->body('An AVN with the title "'.$record->title.'" already exists.')
                                ->warning()
                                ->send();

                            return;
                        }

                        Avn::create([
                            'title' => $record->title,
                            'version' => $record->version,
                            'cover_image' => $record->cover_image,
                            'socigames_url' => $record->url,
                            'last_updated_on_itch' => $record->release_date,
                            'status_id' => 7,
                            'rating' => 0,
                        ]);

                        Notification::make()
                            ->title('Imported successfully')
                            ->body('"'.$record->title.'" has been added to your AVN collection.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
