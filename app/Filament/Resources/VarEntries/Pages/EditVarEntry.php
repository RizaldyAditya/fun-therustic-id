<?php

namespace App\Filament\Resources\VarEntries\Pages;

use App\Filament\Resources\VarEntries\VarEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVarEntry extends EditRecord
{
    protected static string $resource = VarEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
