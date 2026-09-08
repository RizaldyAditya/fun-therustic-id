<?php

namespace App\Filament\Resources\SocigamesRss\Pages;

use App\Filament\Resources\SocigamesRss\SocigamesRssResource;
use App\Filament\Resources\SocigamesRss\Tables\SocigamesRssTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListSocigamesRss extends ListRecords
{
    protected static string $resource = SocigamesRssResource::class;

    public function table(Table $table): Table
    {
        return SocigamesRssTable::configure($table);
    }
}
