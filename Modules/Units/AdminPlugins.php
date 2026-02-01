<?php

namespace Units;

use Filament\Contracts\Plugin;
use Filament\Panel;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Units\ActLog\Admin\ActLogAdminPlugin;
use Units\Auth\Admin\AuthAdminPlugin;
use Units\Avatar\Admin\AdminAvatarPlugin;
use Units\BankLetterTemplate\Admin\BankLetterTemplateAdminPlugin;
use Units\Corporates\FieldOfActivity\Admin\CorporateFieldsOfActivityAdminPlugin;
use Units\Dashboard\Admin\AdminDashboardPlugin;
use Units\Financier\BranchEmployee\Admin\BranchEmployeeAdminPlugin;
use Units\Financier\Financier\Admin\FinancierAdminPlugin;
use Units\Financier\FinancierBranch\Admin\FinancierBranchAdminPlugin;
use Units\Financier\FinancierType\Admin\FinancierTypeAdminPlugin;
use Units\Financier\FinancingMode\Admin\FinancingModeAdminPlugin;
use Units\MemorandumTemplates\Admin\MemorandumTemplatesAdminPlugin;
use Units\Settings\Admin\AdminSettingsPlugin;
use Units\Shield\Admin\AdminShieldPlugin;

class AdminPlugins implements Plugin
{
    public static function make()
    {
        return new static;
    }

    public function getId(): string
    {
        return 'filament-admin-plugins';
    }

    public function register(Panel $panel): void
    {
        $panel->plugins(
            [
                AdminAvatarPlugin::make(),
                AdminDashboardPlugin::make(),
                AuthAdminPlugin::make(),
                ActLogAdminPlugin::make(),
                AdminShieldPlugin::make(),
                SpotlightPlugin::make(),
                AdminSettingsPlugin::make(),
            ]
        );
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.`
    }
}
