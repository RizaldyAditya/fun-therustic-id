<?php

namespace App\Filament\Resources\SocigamesRss\Tables;

use App\Models\Avn;
use App\Models\Genre;
use Filament\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

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
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->schema([
                                ViewField::make('image_preview')
                                    ->view('filament.image-preview')
                                    ->viewData(fn ($record) => [
                                        'image' => $record->cover_image,
                                    ]),
                            ])
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

                Tables\Columns\IconColumn::make('imported_status')
                    ->label('Imported')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => Avn::where('title', $record->title)->exists())
                    ->boolean(),

                Tables\Columns\TextColumn::make('avn_version')
                    ->label('AVN Version')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => Avn::where('title', $record->title)->value('version'))
                    ->placeholder('—')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('developer')
                    ->label('Developer')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('genres')
                    ->label('Genres')
                    ->wrap(),

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
                    ->modalSubmitActionLabel('Import')
                    ->schema([
                        ViewField::make('import_preview')
                            ->view('filament.socigames-import-preview')
                            ->viewData(fn ($record) => [
                                'cover_image' => $record->cover_image,
                                'title' => $record->title,
                                'version' => $record->version,
                                'developer' => $record->developer,
                                'genres' => $record->genres,
                                'engine' => $record->engine,
                                'description' => $record->description,
                                'release_date' => $record->release_date,
                            ]),
                    ])
                    ->action(function ($record) {
                        $existingAvn = Avn::where('title', $record->title)->first();

                        if ($existingAvn) {
                            $existingAvn->update([
                                'version' => $record->version,
                                'last_updated_date' => $record->release_date,
                            ]);

                            Notification::make()
                                ->title('Already exists')
                                ->body('"'.$record->title.'" already exists. Version and last updated date have been updated.')
                                ->info()
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
                            'last_updated_date' => $record->release_date,
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
