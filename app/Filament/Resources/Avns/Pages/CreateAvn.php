<?php
namespace App\Filament\Resources\Avns\Pages;

use App\Filament\Resources\Avns\AvnResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAvn extends CreateRecord
{
    protected static string $resource = AvnResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
