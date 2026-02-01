<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:16 AM
 */

namespace Units\Users\Manage\My;

use Filament\Contracts\Plugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\Widgets\AccountWidget;
use Units\Auth\My\Filament\Components\LoginComponent;
use Units\Auth\My\Filament\Pages\Auth\Register\LegalPage;
use Units\Auth\My\Filament\Pages\Auth\Register\NaturalPage;
use Units\Auth\My\Filament\Pages\Auth\Register\SelectPersonTypePage;
use Units\Auth\My\Filament\Pages\Auth\Register\Show;
use Units\Auth\My\Filament\Pages\Auth\VerifyPage;
use Units\Auth\My\Filament\Pages\ProfilePage;
use Units\Users\Manage\My\Filament\Resources\UserResource;


class ManagePanel_My_UsersPlugin implements Plugin
{

    public function getId(): string
    {
        return 'filament-manage-my-users-plugin';
    }

    public static function make()
    {
        return new static();
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                UserResource::class
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
