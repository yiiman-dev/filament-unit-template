<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/12/25, 4:40 AM
 */

namespace Modules\Basic\BaseKit\Filament;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

trait HasNotification
{

    public function alert_error(string $message,string $title='',$actions=[]):void
    {
        static::static_alert_error($message,$title,$actions);
    }


    public function alert_success(string $message,string $title=''):void
    {
        static::static_alert_success($message,$title);
    }



    public function alert_info(string $message,string $title=''):void
    {
        static::static_alert_info($message,$title);
    }


    public function alert_warning(string $message,string $title=''):void
    {
        static::static_alert_warning($message,$title);
    }


    public static function static_alert_error(string $message, string $title = '',array $actions=[]): void
    {
        Log::error('Show error alert to user ('.auth()?->user()?->phone_number.') : '.$message);
        Notification::make('error_'.uniqid())
            ->danger()
            ->actions($actions)
            ->title($title)
            ->body($message)
            ->send();
    }


    public static function static_alert_success(string $message, string $title = ''): void
    {
        Notification::make('success_'.uniqid())
            ->success()
            ->title($title)
            ->body($message)
            ->send();
    }


    public static function static_alert_info(string $message, string $title = ''): void
    {
        Notification::make('info_'.uniqid())
            ->info()
            ->title($title)
            ->body($message)
            ->send();
    }


    public static function static_alert_warning(string $message, string $title = ''): void
    {
        Log::warning('Show warning alert to user ('.auth()?->user()?->phone_number.') : '.$message);
        Notification::make('warning_'.uniqid())
            ->warning()
            ->title($title)
            ->body($message)
            ->send();
    }




}
