<?php
namespace App\Filament\Resources\Streams\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;

class StreamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpan('full')
                    ->schema([
                        Section::make('')
                            ->schema([
                                TextInput::make('name')->required()->inlineLabel()->autofocus(),
                                TextInput::make('label')->required()->inlineLabel(),
                                TextInput::make('homepage_url')->url()->required()->inlineLabel(),
                                FileUpload::make('logo')->inlineLabel()
                            ]),
                        Section::make('')
                            ->schema([
                                Toggle::make('is_crawlable')->required()->inlineLabel(),
                                Toggle::make('is_active')->required()->inlineLabel()
                            ])
                    ])
            ]);
    }
}
