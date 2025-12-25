<?php
namespace App\Filament\Resources\Statuses\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ColorPicker;

class StatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')->required()->unique()->inlineLabel()->autofocus(),
                        TextInput::make('slug')->required()->unique()->inlineLabel(),
                        ColorPicker::make('text_color')->default('#000000')->inlineLabel(),
                        ColorPicker::make('bg_color')->default('#ffffff')->inlineLabel(),
                        Toggle::make('is_active')->required()->default(true)->inlineLabel(),
                        TextInput::make('order')->default(0)->required()->numeric()->inlineLabel(),
                    ])
            ]);
    }
}
