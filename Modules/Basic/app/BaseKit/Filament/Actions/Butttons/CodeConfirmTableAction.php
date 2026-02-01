<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Modules\Basic\BaseKit\Filament\Actions\Butttons;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;
use Modules\Basic\BaseKit\Filament\HasNotification;

class CodeConfirmTableAction
{
    use HasNotification;

    public static function make($key = 'confirm', ?callable $action = null, $form = [])
    {
        $generated_code = rand(1111, 9999);

        return Action::make($key)
            ->label('تایید با کد ۴ رقمی')
            ->modalHeading('ورود کد تایید')
            ->modalDescription(new HtmlString("<h2 style='direction: rtl'>لطفا جهت تایید عملیات٬ کد <strong style='font-weight: 900;font-size: x-large'>$generated_code</strong> را درون کادر ذیل وارد کنید.</h2>"))
            ->modalSubmitActionLabel('تایید')
            ->modalCancelActionLabel('انصراف')
            ->form([
                TextInput::make('verification_code')
                    ->label('کد ۴ رقمی')
                    ->required()
                    ->length(7) // طول دقیق 4 رقم
                    ->mask('*-*-*-*')
                    ->extraAlpineAttributes([
                        'style' => 'font-size: large;text-align:center;direction:ltr;',
                    ])
                    ->columns(1)
                    ->maxWidth('50%'),
                TextInput::make('hide')
                    ->extraAlpineAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->extraFieldWrapperAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->hiddenLabel()
                    ->default($generated_code),
                ...$form,
            ])
            ->action(function (array $data, $record) use ($action) {
                // $data['verification_code'] شامل کد وارد شده توسط کاربر است

                // اینجا منطق اعتبارسنجی کد را قرار دهید
                $code = str_replace(['-', '*'], '', $data['verification_code']);

                if ((string) $code === (string) $data['hide']) { // مثال اعتبارسنجی ساده
                    // عملیات تایید موفق
                    if (! empty($action)) {
                        $action($data, $record);
                    }
                } else {
                    // در صورت خطا می‌توانید خطا ارسال کنید یا پیام دهید
                    Notification::make('danger_'.uniqid())
                        ->danger()
                        ->title('خطا')
                        ->body('کد تایید اشتباه است.')
                        ->send();
                    // اگر بخواهید Modal بسته نشود می‌توانید استثنا پرتاب کنید:
                    // throw new \Exception('کد تایید اشتباه است.');
                }
            });
    }
}
