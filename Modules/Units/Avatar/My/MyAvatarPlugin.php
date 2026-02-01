<?php

namespace Units\Avatar\My;

use Filament\Contracts\Plugin;
use Filament\Panel;

class MyAvatarPlugin implements Plugin
{

    public static function make (){
        return new self();
    }
    public function getId(): string
    {
        return 'my-avatar-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel->defaultAvatarProvider(MyAvatarProvider::class);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
