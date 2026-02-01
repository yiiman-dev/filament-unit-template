<?php

namespace Units\Dashboard\Manage;


use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Dashboard\Manage\Filament\Pages\Dashboard;
use Units\Dashboard\Manage\Widgets\AverageWaitingTimeBarChartWidget;
use Units\Dashboard\Manage\Widgets\FinancialProcessTrackingWidget;
use Units\Dashboard\Manage\Widgets\ProcessStatsOverviewWidget;
use Units\Dashboard\Manage\Widgets\ProcessStatusTrackingWidget;
use Units\Dashboard\Manage\Widgets\ResourceValueByToolTypeRadarChartWidget;
use Units\Dashboard\Manage\Widgets\ResourceValueDonutChartWidget;

class ManageDashboardPlugin implements Plugin
{
    public function getId(): string
    {
        return 'manage-dashboard-plugin';
    }

    public static function make()
    {
        return new self();
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Dashboard::class
            ])
            ->widgets([
                ProcessStatsOverviewWidget::class,
                ResourceValueDonutChartWidget::class,
                ResourceValueByToolTypeRadarChartWidget::class,
                AverageWaitingTimeBarChartWidget::class,
                FinancialProcessTrackingWidget::class,
                ProcessStatusTrackingWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
