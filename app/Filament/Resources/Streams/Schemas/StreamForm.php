<?php
namespace App\Filament\Resources\Streams\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                                FileUpload::make('logo')
                                    ->disk('public')
                                    ->directory('img/logos')
                                    ->visibility('public')
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
