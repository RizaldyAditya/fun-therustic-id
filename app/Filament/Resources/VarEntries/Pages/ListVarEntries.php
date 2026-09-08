<?php

namespace App\Filament\Resources\VarEntries\Pages;

use App\Filament\Resources\VarEntries\VarEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVarEntries extends ListRecords
{
    protected static string $resource = VarEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
