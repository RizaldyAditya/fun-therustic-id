<?php

namespace App\Filament\Resources\Donghuas\Pages;

use App\Filament\Resources\Donghuas\DonghuaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDonghuas extends ListRecords
{
    protected static string $resource = DonghuaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
