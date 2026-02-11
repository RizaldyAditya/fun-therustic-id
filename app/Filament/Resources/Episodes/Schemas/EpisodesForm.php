<?php
namespace App\Filament\Resources\Episodes\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class EpisodesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(2)
                    ->columnSpan('full')
                    ->schema([
                        Section::make('Donghua')
                            ->schema([
                                Select::make('donghua_id')
                                    ->label('Title')
                                    ->relationship('donghua', 'title_en')
                                    ->searchable()
                                    ->required(),
                                TextInput::make('episode_number')->label('Episode Number')->numeric()->required(),
                                TextInput::make('title')->label('Episode Title'),
                            ]),
                        Section::make('Streaming Web')
                            ->schema([
                                Select::make('stream_id')
                                    ->relationship('stream', 'name')
                                    ->required(),
                                TextInput::make('stream_url')->label('Stream URL')->required(),
                                TextInput::make('video_source_url')->label('Video Source URL')->required(),
                            ]),
                        Section::make('Notes')
                            ->schema([
                                Textarea::make('notes')
                                    ->label('Episode Notes')
                                    ->rows(5),
                            ])
                    ]),
            ]);
    }
}
