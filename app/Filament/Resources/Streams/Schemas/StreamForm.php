<?php

namespace App\Filament\Resources\Streams\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StreamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('homepage_url')
                    ->url(),
                Textarea::make('logo')
                    ->columnSpanFull(),
                Toggle::make('is_crawlable')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
