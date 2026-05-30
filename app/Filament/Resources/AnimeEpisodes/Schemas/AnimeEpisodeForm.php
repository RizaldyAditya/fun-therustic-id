<?php

namespace App\Filament\Resources\AnimeEpisodes\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnimeEpisodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(2)
                    ->columnSpan('full')
                    ->schema([
                        Section::make('Description')
                            ->icon('heroicon-s-clipboard-document-list')
                            ->iconColor('success')
                            ->schema([
                                Select::make('anime_id')
                                    ->label('Anime Title')
                                    ->relationship('anime', 'title')
                                    ->required()
                                    ->searchable()
                                    ->inlineLabel(),
                                TextInput::make('title')
                                    ->label('Episode Title')
                                    ->required()
                                    ->inlineLabel(),
                                TextInput::make('episode_number')
                                    ->label('Episode Number')
                                    ->type('number')
                                    ->required()
                                    ->inlineLabel(),
                                Textarea::make('notes')->rows(4),
                                Toggle::make('is_active')
                                    ->label('Status')
                                    ->required()
                                    ->default(true),
                            ]),
                        Section::make('Streaming Sources')
                            ->icon('heroicon-s-play')
                            ->iconColor('info')
                            ->schema([
                                Select::make('stream_id')
                                    ->relationship('stream', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->inlineLabel(),
                                TextInput::make('stream_url')
                                    ->label('Stream URL')
                                    ->url()
                                    ->prefixIcon('heroicon-s-globe-alt')
                                    ->suffixAction(
                                        Action::make('openStreamUrlInNewTab')
                                            ->url(fn ($record) => $record->stream_url ?? null)
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->tooltip('Open this stream website in new tab')
                                            ->openUrlInNewTab()
                                            ->hidden(fn ($state) => empty($state)),
                                    )
                                    ->inlineLabel(),
                                TextInput::make('video_url')
                                    ->label('Video Source / Embed URL')
                                    ->url()
                                    ->prefixIcon('heroicon-s-globe-alt')
                                    ->suffixAction(
                                        Action::make('openVideoEmbedInNewTab')
                                            ->url(fn ($record) => $record->video_url ?? null)
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->tooltip('Open this video embed in new tab')
                                            ->openUrlInNewTab()
                                            ->hidden(fn ($state) => empty($state)),
                                    )
                                    ->inlineLabel(),
                                Select::make('subtitle_lang')
                                    ->label('Subtitle Language')
                                    ->options([
                                        'en' => 'English',
                                        'id' => 'Indonesian',
                                        'und' => 'Unspecified',
                                    ])
                                    ->inlineLabel(),
                            ]),
                    ]),
            ]);
    }
}
