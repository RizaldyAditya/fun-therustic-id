<?php

namespace App\Filament\Resources\Episodes\Pages;

use App\Filament\Resources\Episodes\EpisodesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEpisodes extends CreateRecord
{
    protected static string $resource = EpisodesResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
