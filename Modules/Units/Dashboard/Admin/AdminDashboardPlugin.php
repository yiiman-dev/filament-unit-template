<?php

namespace Units\Dashboard\Admin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Dashboard\Admin\Widgets\FinanceRequestAmountWidget;

class AdminDashboardPlugin implements Plugin
{

    public function getId(): string
    {
        return 'admin-dashboard';
    }

    public static function make()
    {
        return new self();
    }

    public function register(Panel $panel): void
    {
        $panel->widgets([
            FinanceRequestAmountWidget::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
