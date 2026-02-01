<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          1/15/25, 12:00 PM
 */

namespace Units\Dashboard\Manage\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

/**
 * Dashboard page for Manage panel
 *
 * This class extends the base Filament Dashboard to provide custom functionality
 * and access to widget management methods.
 *
 * @see \Filament\Pages\Dashboard
 */
class Dashboard extends BaseDashboard
{
    /**
     * Get the number of columns for the dashboard widgets
     *
     * @return int|string|array
     */
    public function getColumns(): int|string|array
    {
        return 12; // 12 ستون برای تقسیم دقیق فضا
    }

    /**
     * Get widgets for the dashboard
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }
}
