<?php

namespace App\Filament\Resources\Streams\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StreamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(2)
                    ->columnSpan('full')
                    ->schema([
                        Section::make('')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->inlineLabel()
                                    ->placeholder('AnimeXin')
                                    ->autofocus(),
                                TextInput::make('label')
                                    ->required()
                                    ->inlineLabel()
                                    ->placeholder('animexin'),
                                TextInput::make('homepage_url')
                                    ->label('Homepage URL')
                                    ->url()
                                    ->required()
                                    ->inlineLabel()
                                    ->placeholder('https://animexin.dev/'),
                                TextInput::make('fetchStreamLogo')
                                    ->label('Fetch Website Streaming Logo from URL')
                                    ->placeholder('https://example.com/cover.jpg')
                                    ->helperText('Paste a URL and click the download icon to set as cover.')
                                    ->inlinelabel()
                                    ->suffixAction(
                                        Action::make('fetchCover')
                                            ->icon('heroicon-m-arrow-down-tray')
                                            ->color('success')
                                            ->action(function ($state, $set) {
                                                if (empty($state)) {
                                                    return;
                                                }

                                                try {
                                                    $response = Http::get($state);
                                                    if (! $response->successful()) {
                                                        throw new \Exception('URL unreachable');
                                                    }

                                                    $extension = pathinfo(parse_url($state, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                                    $filename = 'img/logos/'.Str::random(40).'.'.$extension;
                                                    Storage::disk('public')->put($filename, $response->body());
                                                    $set('logo', $filename);
                                                    $set('fetchStreamLogo', null);

                                                    Notification::make()
                                                        ->title('Stream Logo downloaded!')
                                                        ->success()
                                                        ->send();

                                                } catch (\Exception $e) {
                                                    Notification::make()
                                                        ->title('Fetch Stream Logo Failed')
                                                        ->body($e->getMessage())
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                    ),
                                FileUpload::make('logo')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('img/logos')
                                    ->image()
                                    ->inlineLabel(),
                            ]),
                        Section::make('')
                            ->schema([
                                Toggle::make('is_cover_image')
                                    ->label('Poster Fetch')
                                    ->default(false)
                                    ->required(),
                                Toggle::make('is_crawlable')
                                    ->label('Crawlable')
                                    ->default(false)
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
