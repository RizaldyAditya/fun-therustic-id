<?php

namespace App\Filament\Resources\Avns;

use App\Filament\Resources\Avns\Pages\CreateAvn;
use App\Filament\Resources\Avns\Pages\EditAvn;
use App\Filament\Resources\Avns\Pages\ListAvns;
use App\Filament\Resources\Avns\Schemas\AvnForm;
use App\Filament\Resources\Avns\Tables\AvnsTable;
use App\Models\Avn;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AvnResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Visual Novels';

    protected static ?int $navigationSort = 4;

    protected static ?string $model = Avn::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'AVNs';

    public static function form(Schema $schema): Schema
    {
        return AvnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvnsTable::configure($table);
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
            'index' => ListAvns::route('/'),
            'create' => CreateAvn::route('/create'),
            'edit' => EditAvn::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
