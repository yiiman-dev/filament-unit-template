<?php

return [
    App\Providers\AppServiceProvider::class,
    \Units\Chat\Providers\ChatServiceProvider::class,
    \Units\Panels\Manage\Providers\Filament\ManagePanelProvider::class,
    \Units\Panels\Admin\Providers\Filament\AdminPanelProvider::class,
    \Modules\Basic\Providers\BasicServiceProvider::class,
    \Units\Panels\Manage\Providers\FilamentManageServiceProvider::class,
    \Units\Panels\Admin\Providers\FilamentAdminServiceProvider::class,

    \Units\Auth\Common\Providers\AuthServiceProvider::class,
    App\Providers\VoltServiceProvider::class,

    // Additional Service Providers...
];
