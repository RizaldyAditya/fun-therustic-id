<?php

namespace App\Filament\Resources\Sources\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class SourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID')->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')->sortable()->searchable(),
                IconColumn::make('is_active')->label('Active')->alignEnd()->boolean(),
            ])
            ->recordActions([
                EditAction::make()->label('')->tooltip('Edit'),
                DeleteAction::make()->label('')->tooltip('Delete'),
            ])
            ->toolbarActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ])
            ->defaultSort('id', 'asc');
    }
}
