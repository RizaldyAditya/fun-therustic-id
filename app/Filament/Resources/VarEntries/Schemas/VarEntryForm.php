<?php

namespace App\Filament\Resources\VarEntries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VarEntryForm
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
                            ->autofocus()
                            ->helperText('Unique identifier for this variable'),
                        Textarea::make('value')
                            ->required()
                            ->inlineLabel()
                            ->rows(3)
                            ->helperText('The value to store'),
                        Select::make('group')
                            ->options([
                                'AVN' => 'AVN',
                                'Donghua' => 'Donghua',
                                'Anime' => 'Anime',
                                'General' => 'General',
                            ])
                            ->inlineLabel()
                            ->helperText('Group for organizing variables'),
                    ]),
            ]);
    }
}
