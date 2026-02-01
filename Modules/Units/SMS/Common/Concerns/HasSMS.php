<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/7/25, 3:40 PM
 */

namespace Units\SMS\Common\Concerns;

use Modules\Basic\BaseKit\Filament\InteractWithCorporate;
use Modules\Basic\Helpers\Helper;
use Units\SMS\Common\Services\BaseSmsService;

trait HasSMS
{
    use InteractWithCorporate {
        getCorporateCEOModel as private;
    }

    private BaseSmsService $sms_service;

    public function initialHasSms()
    {
        $this->sms_service = new BaseSmsService();
    }

    /**
     * ارسال پیامک به یوزر لاگین شده در همین پنل
     * @param $text
     * @return void
     */
    public function SendSmsToCurrentUser($text): void
    {
        $this->sms_service->sendSms(Helper::normalize_phone_number(filament()->auth()->user()->phone_number), $text);
    }

    public function SendSmsToCurrentCorporateCEO($text): void
    {
        $ceo = $this->getCorporateCEOModel();
        if ($ceo && $ceo->phone_number) {
            $this->sms_service->sendSms(Helper::normalize_phone_number($ceo->phone_number), $text);
        }
    }


}
