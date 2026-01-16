<?php

namespace App\Filament\Resources\AnimeEpisodes\Pages;

use App\Filament\Resources\AnimeEpisodes\AnimeEpisodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnimeEpisodes extends ListRecords
{
    protected static string $resource = AnimeEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
