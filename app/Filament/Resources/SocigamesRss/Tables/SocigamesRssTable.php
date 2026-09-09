<?php

namespace App\Filament\Resources\SocigamesRss\Tables;

use App\Models\Avn;
use App\Models\Genre;
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
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

                Tables\Columns\TextColumn::make('developer')
                    ->label('Developer')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('genres')
                    ->label('Genres')
                    ->limit(40),

                Tables\Columns\TextColumn::make('engine')
                    ->label('Engine')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('release_date')
                    ->label('Release Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
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
                    ->modalWidth('3xl')
                    ->modalContent(fn ($record) => new HtmlString(
                        '<div class="space-y-4">'
                            .($record->cover_image
                                ? '<img src="'.e(Str::startsWith($record->cover_image, ['http://', 'https://']) ? $record->cover_image : asset('storage/'.$record->cover_image)).'" class="w-full rounded-lg" />'
                                : '')
                            .'<div class="rounded-lg border p-4 space-y-2">'
                                .'<p><strong>Title:</strong> '.e($record->title).'</p>'
                                .'<p><strong>Version:</strong> '.e($record->version ?? 'N/A').'</p>'
                                .'<p><strong>Developer:</strong> '.e($record->developer ?? 'N/A').'</p>'
                                .'<p><strong>Genres:</strong> '.e($record->genres ?? 'N/A').'</p>'
                                .'<p><strong>Engine:</strong> '.e($record->engine ?? 'N/A').'</p>'
                                .'<p><strong>Description:</strong> '.e($record->description ?? 'N/A').'</p>'
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

                        $genreId = null;
                        if ($record->genres) {
                            $firstGenre = trim(explode(',', $record->genres)[0]);
                            $genre = Genre::firstOrCreate(
                                ['name' => $firstGenre],
                            );
                            $genreId = $genre->id;
                        }

                        Avn::create([
                            'title' => $record->title,
                            'version' => $record->version,
                            'developer' => $record->developer,
                            'description' => $record->description,
                            'cover_image' => $record->cover_image,
                            'socigames_url' => $record->url,
                            'genre_id' => $genreId,
                            'engine' => $record->engine,
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
