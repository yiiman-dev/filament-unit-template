<?php

namespace Units\Avatar\Admin;

use Filament\Contracts\Plugin;
use Filament\Panel;

class AdminAvatarPlugin implements Plugin
{

    public static function make(){
        return new self();
    }

    public function getId(): string
    {
        return 'admin-avatar-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel->defaultAvatarProvider(AdminAvatarProvider::class);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
