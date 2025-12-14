<?php

namespace App\Filament\Resources\Episodes\Pages;

use App\Filament\Resources\Episodes\EpisodesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEpisodes extends ListRecords
{
    protected static string $resource = EpisodesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
