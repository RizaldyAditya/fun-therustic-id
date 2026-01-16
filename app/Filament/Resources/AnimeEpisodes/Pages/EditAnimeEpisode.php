<?php

namespace App\Filament\Resources\AnimeEpisodes\Pages;

use App\Filament\Resources\AnimeEpisodes\AnimeEpisodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnimeEpisode extends EditRecord
{
    protected static string $resource = AnimeEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
