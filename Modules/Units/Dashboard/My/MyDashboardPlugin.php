<?php

namespace Units\Dashboard\My;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Dashboard\My\Filament\Pages\Dashboard;
use Units\Dashboard\My\Widgets\FinanceRequestAmountWidget;
use Units\Dashboard\My\Widgets\MemorandumRequestAmountWidget;

class MyDashboardPlugin implements Plugin
{

    public function getId(): string
    {
        return 'my-dashboard-plugin';
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
                FinanceRequestAmountWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
