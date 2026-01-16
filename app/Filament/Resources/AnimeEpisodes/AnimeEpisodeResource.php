<?php

namespace App\Filament\Resources\AnimeEpisodes;

use App\Filament\Resources\AnimeEpisodes\Pages\CreateAnimeEpisode;
use App\Filament\Resources\AnimeEpisodes\Pages\EditAnimeEpisode;
use App\Filament\Resources\AnimeEpisodes\Pages\ListAnimeEpisodes;
use App\Filament\Resources\AnimeEpisodes\Schemas\AnimeEpisodeForm;
use App\Filament\Resources\AnimeEpisodes\Tables\AnimeEpisodesTable;
use App\Models\AnimeEpisode;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AnimeEpisodeResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Movies';
    protected static ?int $navigationSort                      = 5;
    protected static ?string $model                            = AnimeEpisode::class;
    protected static ?string $recordTitleAttribute             = 'title';
    protected static ?string $modelLabel                       = 'Anime Episode';

    public static function form(Schema $schema): Schema
    {
        return AnimeEpisodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnimeEpisodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnimeEpisodes::route('/'),
            'create' => CreateAnimeEpisode::route('/create'),
            'edit' => EditAnimeEpisode::route('/{record}/edit'),
        ];
    }
}
