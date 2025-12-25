<?php
namespace App\Filament\Resources\Statuses\Tables;

use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Collection;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Filters\TernaryFilter;

class StatusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')->sortable()->alignCenter()->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('slug')->sortable()->searchable()->badge(),
                ColorColumn::make('text_color')->label('Text Color')->alignCenter(),
                ColorColumn::make('bg_color')->label('Background Color')->alignCenter(),
                IconColumn::make('is_active')->label('Active')->alignEnd()->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make()->label(''),
                DeleteAction::make()->label('')
            ])
            ->toolbarActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $records->each->delete()),
            ])
            ->defaultSort('order', 'asc')
            ->recordUrl(null);
    }
}
