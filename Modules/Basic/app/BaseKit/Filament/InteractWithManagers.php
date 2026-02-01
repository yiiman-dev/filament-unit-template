<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/7/25, 4:14 PM
 */

namespace Modules\Basic\BaseKit\Filament;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Units\ActLog\Manage\Models\ActLog;
use Units\Auth\Manage\Models\UserModel;

/**
 *
 */
trait InteractWithManagers
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getLatestActiveUser()
    {
        return ActLog::latest()->first();
    }

    public function sendErrorNotificationToAllManagers($title, $message): void
    {
        $managers = UserModel::all();
        Notification::make(Str::uuid())
            ->danger()
            ->sendToDatabase($managers)
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead(),
                Action::make('markAsUnread')
                    ->button()
                    ->markAsUnread(),
            ])
            ->title($title)
            ->body($message)
            ->send();
    }


    public function sendInfoNotificationToAllManagers($title, $message): void
    {
        $managers = UserModel::all();
        Notification::make(Str::uuid())
            ->info()
            ->title($title)
            ->body($message)
            ->sendToDatabase($managers);
    }


    public function sendDangerNotificationToAllManagers($title, $message): void
    {
        $managers = UserModel::all();
        Notification::make(Str::uuid())
            ->danger()
            ->title($title)
            ->body($message)
            ->sendToDatabase($managers);;
    }


    public function sendSuccessNotificationToAllManagers($title, $message): void
    {
        $managers = UserModel::all();
        Notification::make(Str::uuid())
            ->success()
            ->title($title)
            ->body($message)
            ->sendToDatabase($managers);
    }


}
