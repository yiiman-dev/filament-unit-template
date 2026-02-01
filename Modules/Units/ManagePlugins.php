<?php

namespace Units;

use EightyNine\FilamentPageAlerts\FilamentPageAlertsPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Units\ActLog\Manage\ActLogManagePlugin;
use Units\Auth\Manage\AuthManagePlugin;
use Units\Avatar\Manage\ManageAvatarPlugin;
use Units\BankLetter\Manage\BankLetterManagePlugin;
use Units\BankLetterTemplate\Manage\BankLetterTemplateManagePlugin;
use Units\BusinessCoworkers\Manage\BusinessCoworkersManagePlugin;
use Units\Corporates\Placed\Manage\CorporatePlacedManagePlugin;
use Units\Corporates\Registering\Manage\CorporateRegisteringManagePlugin;
use Units\Corporates\Users\Manage\CorporateUsersManagePlugin;
use Units\Dashboard\Manage\ManageDashboardPlugin;
use Units\DocumentConditionTemplate\Manage\DocumentConditionTemplateManagePlugin;
use Units\DocumentTypes\Manage\DocumentTypeManagePlugin;
use Units\Enactment\Execution\Manage\EnactmentExecutionManagePlugin;
use Units\Enactment\Operation\Manage\EnactmentOperationManagePlugin;
use Units\Enactment\Request\Manage\ManageEnactmentRequestPlugin;
use Units\FinanceRequest\Manage\FinanceRequestManagePlugin;
use Units\Financier\BranchEmployee\Manage\BranchEmployeeManagePlugin;
use Units\Financier\Financier\Manage\FinancierManagePlugin;
use Units\Financier\FinancierBranch\Manage\FinancierBranchManagePlugin;
use Units\Financier\FinancingMode\Manage\FinancingModeManagePlugin;
use Units\Invoice\Manage\InvoiceManagePlugin;
use Units\Memorandum\Request\Manage\MemorandumRequestManagePlugin;
use Units\Memorandum\Sign\Manage\ManageMemorandumSigningPlugin;
use Units\MemorandumTemplates\Admin\MemorandumTemplatesAdminPlugin;
use Units\Settings\Manage\ManageSettingsPlugin;
use Units\Shield\Manage\ManageShieldPlugin;
use Units\Users\Manage\ManageUserManagementPlugin;
use Units\Users\Manage\My\ManagePanel_My_UsersPlugin;

class ManagePlugins implements Plugin
{
    public static function make()
    {
        return new static;
    }

    public function getId(): string
    {
        return 'filament-manage-plugins';
    }

    public function register(Panel $panel): void
    {
        $panel->plugins([
            ManageAvatarPlugin::make(),
            ManageDashboardPlugin::make(),
            ManageShieldPlugin::make(),
//            ApprovalManagePlugin::make(),
            ManageSettingsPlugin::make(),
            ManageUserManagementPlugin::make(),
            AuthManagePlugin::make(),
            ActLogManagePlugin::make(),
            ManagePanel_My_UsersPlugin::make(),
            FilamentPageAlertsPlugin::make(),
//            ManageKnowledgePlugin::make(),
            SpotlightPlugin::make(),
        ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
