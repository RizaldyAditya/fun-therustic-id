<?php

namespace App\Filament\Resources\Hots\Pages;

use App\Filament\Resources\Hots\HotResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListHots extends ListRecords
{
    protected static string $resource = HotResource::class;

    public function configureTable(Table $table): Table
    {
        return parent::configureTable($table)
            ->recordUrl(null);
    }

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
