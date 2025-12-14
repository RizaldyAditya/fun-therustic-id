<?php

namespace App\Filament\Resources\Streams;

use App\Filament\Resources\Streams\Pages\CreateStream;
use App\Filament\Resources\Streams\Pages\EditStream;
use App\Filament\Resources\Streams\Pages\ListStreams;
use App\Filament\Resources\Streams\Schemas\StreamForm;
use App\Filament\Resources\Streams\Tables\StreamsTable;
use App\Models\Stream;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StreamResource extends Resource
{
    protected static ?string $model = Stream::class;
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('homepage_url')->url()->required(),
                FileUpload::make('logo')
                    ->disk('public')
                    ->directory('img/logos')
                    ->required()
                    ->label('Logo')
                    ->visibility('public')
                    ->image(),
                Forms\Components\Toggle::make('is_crawlable')->required(),
                Forms\Components\Toggle::make('is_active')->required()->default(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->label('ID')->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('homepage_url')->url(fn ($record) => $record->homepage_url)->openUrlInNewTab()->limit(50),
                ImageColumn::make('logo')->disk('public'),
                Tables\Columns\IconColumn::make('is_crawlable')->label('Crawlable')->alignCenter()->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->alignCenter()->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $records->each->delete())
            ])
            ->defaultSort('name', 'asc')
            ->recordUrl(null);
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
            'index' => ListStreams::route('/'),
            'create' => CreateStream::route('/create'),
            'edit' => EditStream::route('/{record}/edit'),
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
