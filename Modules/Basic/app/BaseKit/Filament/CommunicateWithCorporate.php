<?php

namespace Modules\Basic\BaseKit\Filament;

use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Support\Facades\Log;
use Modules\Basic\Helpers\Helper;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;

trait CommunicateWithCorporate
{
    public function alert_corporate_error(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        static::alert_corporate_error($message, $corporate_national_code, $title, $actions);
    }


    public function alert_corporate_success(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        static::static_alert_corporate_success($message, $corporate_national_code, $title, $actions);
    }


    public function alert_corporate_info(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        static::static_alert_corporate_info($message, $corporate_national_code, $title, $actions);
    }


    public function alert_corporate_warning(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        static::static_alert_corporate_warning($message, $corporate_national_code, $title, $actions);
    }


    public static function static_alert_corporate_error(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        try {
            CorporateModel::findByNationalCode($corporate_national_code)->corporateUsers->each(
                function (CorporateUsersModel $model) use ($title, $message, $actions) {
                    Notification::make('error_' . uniqid())
                        ->danger()
                        ->actions($actions)
                        ->title($title)
                        ->body($message)
                        ->sendToDatabase($model->user);
                }
            );
            Log::error(
                'Show error alert to user (' . auth()?->user(
                )?->phone_number . ') on corporate ' . $corporate_national_code . ': ' . $message
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            Helper::static_alert_error('خطایی در ارسال پیام به بنگاه رخ داد');
            return;
        }
    }


    public static function static_alert_corporate_success(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        try {
            CorporateModel::findByNationalCode($corporate_national_code)->corporateUsers->each(
                function (CorporateUsersModel $model) use ($title, $message, $actions) {
                    Notification::make('success_' . uniqid())
                        ->success()
                        ->actions($actions)
                        ->title($title)
                        ->body($message)
                        ->sendToDatabase($model->user);
                }
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            Helper::static_alert_error('خطایی در ارسال پیام به بنگاه رخ داد');
            return;
        }
    }


    public static function static_alert_corporate_info(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        try {
            CorporateModel::findByNationalCode($corporate_national_code)->corporateUsers->each(
                function (CorporateUsersModel $model) use ($title, $message, $actions) {
                    Notification::make('info_' . uniqid())
                        ->info()
                        ->actions($actions)
                        ->title($title)
                        ->body($message)
                        ->sendToDatabase($model->user);
                }
            );
            Log::info(
                'Show info alert to user (' . auth()?->user(
                )?->phone_number . ') on corporate ' . $corporate_national_code . ': ' . $message
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            Helper::static_alert_error('خطایی در ارسال پیام به بنگاه رخ داد');
            return;
        }
    }


    public static function static_alert_corporate_warning(
        string $message,
        $corporate_national_code,
        string $title = '',
        ActionGroup|array $actions = []
    ): void {
        try {
            $corporate = CorporateModel::findByNationalCode($corporate_national_code)->corporateUsers->each(
                function (CorporateUsersModel $model) use ($title, $message, $actions) {
                    Notification::make('warning_' . uniqid())
                        ->warning()
                        ->actions($actions)
                        ->title($title)
                        ->body($message)
                        ->sendToDatabase($model->user);
                }
            );
            Log::warning(
                'Show warning alert to user (' . auth()?->user(
                )?->phone_number . ') on corporate ' . $corporate_national_code . ': ' . $message
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            Helper::static_alert_error('خطایی در ارسال پیام به بنگاه رخ داد');
            return;
        }
    }
}
