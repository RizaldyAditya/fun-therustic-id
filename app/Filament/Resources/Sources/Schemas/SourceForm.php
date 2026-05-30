<?php

namespace App\Filament\Resources\Sources\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->inlineLabel()
                            ->autofocus(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required()
                            ->inlineLabel()
                            ->default(true),
                    ]),
            ]);
    }
}
