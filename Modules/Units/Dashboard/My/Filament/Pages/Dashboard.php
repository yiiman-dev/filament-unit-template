<?php

namespace Units\Dashboard\My\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class Dashboard extends \Filament\Pages\Dashboard
{
    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }
}
