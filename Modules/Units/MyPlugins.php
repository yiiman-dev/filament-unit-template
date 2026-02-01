<?php

namespace Units;

use Filament\Contracts\Plugin;
use Filament\Pages\Dashboard;
use Filament\Panel;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Units\ActLog\My\ActLogMyPlugin;
use Units\Auth\My\AuthMyPlugin;
use Units\Avatar\My\MyAvatarPlugin;
use Units\BusinessCoworkers\My\BusinessCoworkersMyPlugin;
use Units\Corporates\Profile\My\CorporateMyPlugin;
use Units\Corporates\Users\My\CorporateUsersMyPlugin;
use Units\Dashboard\My\MyDashboardPlugin;
use Units\Enactment\Operation\My\EnactmentOperationMyPlugin;
use Units\Enactment\Request\My\MyEnactmentRequestPlugin;
use Units\FinanceRequest\My\FinanceRequestMyPlugin;
use Units\FinanceRequestArchive\My\FinanceRequestArchiveMyPlugin;
use Units\Invoice\My\InvoiceMyPlugin;
use Units\Memorandum\Operational\My\MyMemorandumOperationalPlugin;
use Units\Memorandum\Request\My\MemorandumRequestMyPlugin;
use Units\Shield\My\MyShieldPlugin;

class MyPlugins implements Plugin
{
    public static function make()
    {
        return new static;
    }

    public function getId(): string
    {
        return 'filament-my-plugins';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->plugins([
                MyAvatarPlugin::make(),
                MyDashboardPlugin::make(),
                MyShieldPlugin::make(),
                AuthMyPlugin::make(),
                ActLogMyPlugin::make(),
                SpotlightPlugin::make(),
//                ApprovalManagePlugin::make(),
            ])
            ->widgets([
                //                FilamentInfoWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
