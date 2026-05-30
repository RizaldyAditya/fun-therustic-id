<?php

namespace App\Filament\Resources\Episodes;

use App\Filament\Resources\Episodes\Pages\CreateEpisodes;
use App\Filament\Resources\Episodes\Pages\EditEpisodes;
use App\Filament\Resources\Episodes\Pages\ListEpisodes;
use App\Filament\Resources\Episodes\Schemas\EpisodesForm;
use App\Filament\Resources\Episodes\Tables\EpisodesTable;
use App\Models\Episode;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class EpisodesResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Movies';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Donghua Episodes';

    protected static ?string $model = Episode::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Donghua Episode';

    public static function form(Schema $schema): Schema
    {
        return EpisodesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EpisodesTable::configure($table);
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
            'index' => ListEpisodes::route('/'),
            'create' => CreateEpisodes::route('/create'),
            'edit' => EditEpisodes::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
