<?php

namespace Units\SMS\Common\Services;

use Modules\Basic\Job\SmsSendJob;
use Modules\Basic\Job\SmsVerifyLookupJob;
use Units\Settings\Manage\Models\ManageSettings;
use Units\SMS\Common\Concerns\HasSMS;

class SmsService
{
    use HasSMS;

    /**
     * ارسال پیام با استفاده از قالب های آماده در تنظیمات
     * @param string $receiver
     * @param string $template_key
     * @param array $parameters
     * @return void
     */
    public static function sendMessageFromConfig(string $receiver,string $template_key, array $parameters = []): void
    {
        $smsText = ManageSettings::get('sms.' . $template_key);

        // Replace known parameters
        if (!empty($parameters)) {
            foreach ($parameters as $key => $value) {
                $smsText = str_replace('[' . $key . ']', $value, $smsText);
            }
        }

        // Remove any remaining [key]-style placeholders
        $smsText = preg_replace('/\[[^\]]+\]/', '', $smsText);
        static::voidSend($receiver, $smsText);
    }



    /**
     * Send SMS message asynchronously via queue
     *
     * @param string $normalized_mobile Recipient's mobile number
     * @param string $message SMS message content
     * @param string|null $date Scheduled date (optional)
     * @param string|null $type SMS type (optional)
     * @param string|null $localid Local ID for tracking (optional)
     * @return void
     *@see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest::it_can_activate_corporate_registration()
     */
    public static function voidSend(string $normalized_mobile, string $message, string $date = null, string $type = null, string $localid = null): void
    {
        SmsSendJob::dispatch($normalized_mobile, $message, $date, $type, $localid)
            ->onQueue('sms');
    }

    /**
     * Send SMS to corporate CEO as part of activation process
     *
     * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest::it_can_activate_corporate_registration()
     * @param string $recipient Recipient's mobile number
     * @param string $message SMS message content
     * @return bool Success status
     */
    public static function sendSms(string $recipient, string $message): bool
    {
        static::voidSend($recipient, $message);
        return true;
    }


    public static function voidVerifyLookup( $receptor,
        $token,
        $token2,
        $token3,
        $template,
        $type=null): void
    {
        SmsVerifyLookupJob::dispatch($receptor,
            $token,
            $token2,
            $token3,
            $template,
            $type);
    }

}
