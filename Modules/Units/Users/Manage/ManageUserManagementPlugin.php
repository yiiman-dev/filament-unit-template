<?php

namespace Units\Users\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Users\Manage\My\Filament\Resources\UserResource;

class ManageUserManagementPlugin implements Plugin
{

    public static function make()
    {
        return new static();
    }
    public function getId(): string
    {
        return 'filament-user-management-manage-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            UserResource::class,
            \Units\Users\Manage\Manage\Filament\Resources\UserResource::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
