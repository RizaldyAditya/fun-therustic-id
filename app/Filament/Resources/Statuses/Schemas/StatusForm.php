<?php

namespace App\Filament\Resources\Statuses\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique()
                            ->inlineLabel()
                            ->autofocus(),
                        TextInput::make('slug')
                            ->required()
                            ->unique()
                            ->inlineLabel(),
                        ColorPicker::make('text_color')
                            ->label('Text Color')
                            ->default('#000000')
                            ->inlineLabel(),
                        ColorPicker::make('bg_color')
                            ->label('Background Color')
                            ->default('#ffffff')
                            ->inlineLabel(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required()
                            ->default(true)
                            ->inlineLabel(),
                        TextInput::make('order')
                            ->label('Sort Order')
                            ->default(0)
                            ->required()
                            ->numeric()
                            ->inlineLabel(),
                    ]),
            ]);
    }
}
