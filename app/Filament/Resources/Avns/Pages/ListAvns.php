<?php

namespace App\Filament\Resources\Avns\Pages;

use App\Filament\Resources\Avns\AvnResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAvns extends ListRecords
{
    protected static string $resource = AvnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
