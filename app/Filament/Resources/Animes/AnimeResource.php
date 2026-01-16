<?php

namespace App\Filament\Resources\Animes;

use App\Filament\Resources\Animes\Pages\CreateAnime;
use App\Filament\Resources\Animes\Pages\EditAnime;
use App\Filament\Resources\Animes\Pages\ListAnimes;
use App\Filament\Resources\Animes\Schemas\AnimeForm;
use App\Filament\Resources\Animes\Tables\AnimesTable;
use App\Models\Anime;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AnimeResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Movies';
    protected static ?int $navigationSort                      = 4;
    protected static ?string $model                            = Anime::class;
    protected static ?string $recordTitleAttribute             = 'title';
    protected static ?string $modelLabel                       = 'Anime';

    public static function form(Schema $schema): Schema
    {
        return AnimeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnimesTable::configure($table);
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
            'index' => ListAnimes::route('/'),
            'create' => CreateAnime::route('/create'),
            'edit' => EditAnime::route('/{record}/edit'),
        ];
    }
}
