<?php

namespace App\Filament\Resources\Anime7Rss;

use App\Filament\Resources\Anime7Rss\Pages\ListAnime7Rss;
use App\Filament\Resources\Anime7Rss\Tables\Anime7RssTable;
use App\Models\Anime7Rss;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class Anime7RssResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Movies';

    protected static ?int $navigationSort = 6;

    protected static ?string $model = Anime7Rss::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Anime7 RSS';

    protected static ?string $pluralModelLabel = 'Anime7 RSS';

    protected static ?string $navigationLabel = 'Anime7 RSS';

    protected static string|BackedEnum|null $navigationIcon = null;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return Anime7RssTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnime7Rss::route('/'),
        ];
    }
}
