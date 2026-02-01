<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:32 AM
 */

namespace Units\Chat\My;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Chat\My\Filament\Resources\ChatThreadResource;

/**
 * پلاگین چت مای
 * Chat my plugin
 *
 * پلاگین برای ادغام چت در پنل مای
 * Plugin for integrating chat in my panel
 */
class ChatMyPlugin implements Plugin
{
    public function getId(): string
    {
        return 'chat-my';
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
