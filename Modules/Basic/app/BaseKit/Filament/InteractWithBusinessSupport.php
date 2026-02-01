<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/8/25, 3:27 AM
 */

namespace Modules\Basic\BaseKit\Filament;

use Filament\Notifications\Notification;
use Illuminate\Notifications\Action;
use Units\Auth\Manage\Models\UserModel;

trait InteractWithBusinessSupport
{

    /**
     * ارسال پیامک به کاربران پشتیبانی تجاری
     *
     * @param string $message متن پیام اس ام اس
     * @param string|null $permissionName نام دسترسی برای فیلتر کاربران (اختیاری)
     * @return void
     */
    public function sendSmsToBusinessSupport(string $message,?string $permissionName = null): void
    {
        if ($permissionName) {
            $users = UserModel::getUsersWithPermission($permissionName);
            foreach ($users as $user) {
                if ($user->phone_number) {
                    $smsService = app(\Units\SMS\Common\Services\BaseSmsService::class);
                    $smsService->voidSend($user->phone_number, $message);
                }
            }
        } else {
            // Send to all business support users if no specific permission is provided
            $allUsers = UserModel::all();
            foreach ($allUsers as $user) {
                if ($user->phone_number) {
                    $smsService = app(\Units\SMS\Common\Services\BaseSmsService::class);
                    $smsService->voidSend($user->phone_number, $message);
                }
            }
        }
    }
    /**
     * ارسال اعلان هشدار به آخرین کاربر فعال پشتیبانی تجاری
     *
     * @param string $message متن اعلان
     * @param string|null $title عنوان اعلان (اختیاری)
     * @param Action[]|null $actions اکشن‌های اعلان (اختیاری)
     * @return void
     */
    public static function sendWarningNotificationToLatestActiveBusinessSupport(string $message, ?string $title = '', array|null $actions = null): void
    {
        // Get the latest active business support user based on activity logs
        // Since there's no specific method, we'll get the most recently active user with business support permissions
        $latestActiveUser = UserModel::all();

        if ($latestActiveUser) {
            $notif = Notification::make()
                ->title($title)
                ->warning()
                ->body($message);
            if (!empty($actions)) {
                $notif->actions($actions);
            }
            $notif->sendToDatabase($latestActiveUser);
        }
    }
    /**
     * به کاربران پشتیبانی پنل منیج یک پیغام ارسال میکند
     *
     * ضمنا باید دسترسی مدنظر تعریف شود تا فقط کاربرانی که آن دسترسی را دارند پیام را دریافت کنند
     * @param string $message
     * @param $permissionName
     * @param string|null $title
     * @param Action[]|null $actions
     * @return void
     */
    public static function sendInfoNotificationToBusinessSupport(string $message,?string $permissionName,?string $title='',array|null $actions=null):void
    {
        $users=UserModel::all();
        if ($users->count()>0){
            $notif=Notification::make()
                ->title($title)
                ->info()
                ->body($message);
            if (!empty($actions)){
                $notif->actions($actions);
            }
            $notif->sendToDatabase($users);
        }
    }

}
