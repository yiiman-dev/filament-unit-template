<?php

namespace Units\Avatar\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;


class ManageAvatarPlugin implements Plugin
{

    public static function make(){
        return new self();
    }

    public function getId(): string
    {
        return 'manage-avatar-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel->defaultAvatarProvider(ManageAvatarProvider::class);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
