<?php
namespace App\Filament\Resources\Sources\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class SourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')->required()->inlineLabel()->autofocus(),
                        Toggle::make('is_active')->required()->inlineLabel()->default(true),
                    ])
            ]);
    }
}
