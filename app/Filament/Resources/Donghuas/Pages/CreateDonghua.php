<?php

namespace App\Filament\Resources\Donghuas\Pages;

use App\Filament\Resources\Donghuas\DonghuaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonghua extends CreateRecord
{
    protected static string $resource = DonghuaResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
