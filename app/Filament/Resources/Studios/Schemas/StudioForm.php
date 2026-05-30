<?php

namespace App\Filament\Resources\Studios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')->required()->unique()->inlineLabel()->autofocus(),
                        TextInput::make('url')->url()->label('URL')->required()->unique()->inlineLabel(),
                        Toggle::make('is_active')->label('Active')->required()->default(true)->inlineLabel(),
                    ]),
            ]);
    }
}
