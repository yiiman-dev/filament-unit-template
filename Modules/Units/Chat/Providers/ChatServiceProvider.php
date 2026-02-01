<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 2:14 AM
 */

namespace Units\Chat\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Units\Chat\Common\Livewire\ChatComponent;
use Units\Chat\Common\Repository\ChatRepo;
use Units\Chat\Common\Services\ChatService;

/**
 * ارائه‌دهنده خدمات چت
 * Chat service provider
 *
 * ارائه‌دهنده خدمات برای واحد چت
 * Service provider for chat unit
 */
class ChatServiceProvider extends ServiceProvider
{
    /**
     * بوت‌کردن سرویس‌ها
     * Boot services
     *
     * @return void
     */
    public function boot(): void
    {
        // Register Livewire components for both tags
        Livewire::component('chat', ChatComponent::class);
        Livewire::component('chat-component', ChatComponent::class);

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Common/resources/views', 'chat');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Common/database/migrations');

        // Publish views
        $this->publishes([
            __DIR__ . '/../Common/resources/views' => resource_path('views/vendor/chat'),
        ], 'chat-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../Common/database/migrations' => database_path('migrations'),
        ], 'chat-migrations');
    }

    /**
     * ثبت سرویس‌ها
     * Register services
     *
     * @return void
     */
    public function register(): void
    {
        // Bind ChatRepo
        $this->app->bind(ChatRepo::class, function ($app) {
            return new ChatRepo();
        });

        // Bind ChatService
        $this->app->bind(ChatService::class, function ($app) {
            return new ChatService($app->make(ChatRepo::class));
        });
    }
}
