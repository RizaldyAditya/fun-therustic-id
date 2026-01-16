<?php
namespace App\Filament\Resources\Donghuas;

use App\Filament\Resources\Donghuas\Pages\CreateDonghua;
use App\Filament\Resources\Donghuas\Pages\EditDonghua;
use App\Filament\Resources\Donghuas\Pages\ListDonghuas;
use App\Filament\Resources\Donghuas\Schemas\DonghuaForm;
use App\Filament\Resources\Donghuas\Tables\DonghuasTable;
use App\Models\Donghua;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DonghuaResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Movies';
    protected static ?int $navigationSort                      = 2;
    protected static ?string $model                            = Donghua::class;
    protected static ?string $recordTitleAttribute             = 'title_en';
    protected static ?string $modelLabel                       = 'Donghua';

    public static function form(Schema $schema): Schema
    {
        return DonghuaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonghuasTable::configure($table);
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
            'index'  => ListDonghuas::route('/'),
            'create' => CreateDonghua::route('/create'),
            'edit'   => EditDonghua::route('/{record}/edit'),
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
