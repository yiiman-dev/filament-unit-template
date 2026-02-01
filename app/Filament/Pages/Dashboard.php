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

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

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
     * Get visible widgets for the dashboard
     *
     * This method returns all widgets that should be displayed on the dashboard.
     * You can override this method to customize which widgets are shown.
     *
     * @return array
     */
    public function getVisibleWidgets(): array
    {
        return [
//            \App\Filament\Widgets\ProcessStatsOverviewWidget::class,
//            \App\Filament\Widgets\ResourceValueDonutChartWidget::class,
//            \App\Filament\Widgets\ResourceValueByToolTypeRadarChartWidget::class,
//            \App\Filament\Widgets\AverageWaitingTimeBarChartWidget::class,
        ];
    }

    /**
     * Get widget data
     *
     * @return array
     */
    public function getWidgetData(): array
    {
        return parent::getWidgetData();
    }
}

