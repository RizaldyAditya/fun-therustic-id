<?php

namespace App\Filament\Resources\Studios;

use App\Filament\Resources\Studios\Pages\CreateStudio;
use App\Filament\Resources\Studios\Pages\EditStudio;
use App\Filament\Resources\Studios\Pages\ListStudios;
use App\Filament\Resources\Studios\Schemas\StudioForm;
use App\Filament\Resources\Studios\Tables\StudiosTable;
use App\Models\Studio;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')->required()->unique(),
                Forms\Components\TextInput::make('url')->url(),
                Forms\Components\Toggle::make('is_active')->required()->default(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable()->alignCenter()->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('url')->url(fn ($record) => $record->url)->openUrlInNewTab()->limit(50),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->alignCenter()->boolean()
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
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
            'index' => ListStudios::route('/'),
            'create' => CreateStudio::route('/create'),
            'edit' => EditStudio::route('/{record}/edit'),
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
