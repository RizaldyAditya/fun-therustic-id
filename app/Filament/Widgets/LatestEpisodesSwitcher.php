<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LatestEpisodesSwitcher extends Widget
{
    protected string $view  = 'filament.latest-episodes-switcher';
    public string $viewType = 'card'; // set the default view

    public function setView(string $type)
    {
        $this->viewType = $type;
    }

    protected int | string | array $columnSpan = 'full';
}
