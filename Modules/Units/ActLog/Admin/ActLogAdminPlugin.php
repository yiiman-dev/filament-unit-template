<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\ActLog\Admin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\ActLog\Admin\Filament\Widgets\ProfileUserLogsTableWidget;

class ActLogAdminPlugin implements Plugin
{

    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-act-log-admin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->widgets(
                [
                    ProfileUserLogsTableWidget::class,
                ]
            );
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
