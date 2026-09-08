<?php

namespace App\Filament\Resources\SocigamesRss;

use App\Filament\Resources\SocigamesRss\Pages\ListSocigamesRss;
use App\Filament\Resources\SocigamesRss\Tables\SocigamesRssTable;
use App\Models\SocigamesRss;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SocigamesRssResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Visual Novels';

    protected static ?int $navigationSort = 5;

    protected static ?string $model = SocigamesRss::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'SociGames RSS';

    protected static ?string $pluralModelLabel = 'SociGames RSS';

    protected static ?string $navigationLabel = 'SociGames RSS';

    protected static string|BackedEnum|null $navigationIcon = null;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return SocigamesRssTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSocigamesRss::route('/'),
        ];
    }
}
