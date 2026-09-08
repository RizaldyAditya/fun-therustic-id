<?php

namespace App\Filament\Resources\VarEntries;

use App\Filament\Resources\VarEntries\Pages\CreateVarEntry;
use App\Filament\Resources\VarEntries\Pages\EditVarEntry;
use App\Filament\Resources\VarEntries\Pages\ListVarEntries;
use App\Filament\Resources\VarEntries\Schemas\VarEntryForm;
use App\Filament\Resources\VarEntries\Tables\VarEntriesTable;
use App\Models\VarEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class VarEntryResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $model = VarEntry::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Configuration';

    protected static ?string $navigationLabel = 'Configuration';

    public static function form(Schema $schema): Schema
    {
        return VarEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VarEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVarEntries::route('/'),
            'create' => CreateVarEntry::route('/create'),
            'edit' => EditVarEntry::route('/{record}/edit'),
        ];
    }
}
