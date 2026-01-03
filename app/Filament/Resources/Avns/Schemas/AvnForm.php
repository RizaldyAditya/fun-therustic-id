<?php
namespace App\Filament\Resources\Avns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AvnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('title')
                                    ->inlineLabel()
                                    ->required(),
                                TextInput::make('slug')
                                    ->inlineLabel()
                                    ->required(),
                                TextInput::make('developer')
                                    ->inlineLabel()
                                    ->required(),
                                TextInput::make('version')
                                    ->inlineLabel(),
                                TextInput::make('itch_io_url')
                                    ->label('itch.io URL')
                                    ->url()
                                    ->inlineLabel(),
                            ]),
                    ]),
                Grid::make()
                    ->columns(1)
                    ->schema([
                        Section::make()
                            ->schema([
                                FileUpload::make('cover_image')
                                    ->label('Cover Image')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('img/covers')
                                    ->inlineLabel(),
                                Select::make('status_id')
                                    ->relationship('status', 'name')
                                    ->inlineLabel()
                                    ->required(),

                                DatePicker::make('last_updated_on_itch')
                                    ->label('Last Updated on itch.io')
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->inlineLabel(),
                            ]),
                    ]),
                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make()
                            ->columnSpanFull()
                            ->schema([
                                Textarea::make('description')
                                    ->label(' ')
                                    ->rows(5),
                            ]),
                    ]),
            ]);
    }
}
