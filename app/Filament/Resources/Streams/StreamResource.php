<?php
namespace App\Filament\Resources\Streams;

use App\Filament\Resources\Streams\Pages\CreateStream;
use App\Filament\Resources\Streams\Pages\EditStream;
use App\Filament\Resources\Streams\Pages\ListStreams;
use App\Filament\Resources\Streams\Schemas\StreamForm;
use App\Filament\Resources\Streams\Tables\StreamsTable;
use App\Models\Stream;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StreamResource extends Resource
{
    protected static ?string $model                            = Stream::class;
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $recordTitleAttribute             = 'name';
    protected static ?string $modelLabel                       = 'Stream';

    public static function form(Schema $schema): Schema
    {
        return StreamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StreamsTable::configure($table);
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
            'index'  => ListStreams::route('/'),
            'create' => CreateStream::route('/create'),
            'edit'   => EditStream::route('/{record}/edit'),
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
