<?php

namespace App\Filament\Resources\Hots\Pages;

use App\Filament\Resources\Hots\HotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHot extends EditRecord
{
    protected static string $resource = HotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
