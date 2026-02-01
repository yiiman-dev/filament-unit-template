<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:30 AM
 */

namespace Units\Chat\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Chat\Manage\Filament\Resources\ChatThreadResource;

/**
 * پلاگین مدیریت چت
 * Chat manage plugin
 *
 * پلاگین برای ادغام چت در پنل مدیریت
 * Plugin for integrating chat in manage panel
 */
class ChatManagePlugin implements Plugin
{
    public function getId(): string
    {
        return 'chat-manage';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ChatThreadResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());
        return $plugin;
    }
}
