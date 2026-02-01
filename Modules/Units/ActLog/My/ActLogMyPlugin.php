<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 9:07 AM
 */

namespace Units\ActLog\My;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\ActLog\My\Filament\Widgets\ProfileUserLogsTableWidget;

class ActLogMyPlugin implements Plugin
{

    public static function make()
    {
        return new self();
    }
    public function getId(): string
    {
        return 'filament-act-log-my';
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
