<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 8:18 PM
 */

namespace Units\SMS\Common\Services;

use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\Job\SmsSendJob;
use Modules\Basic\Job\SmsVerifyLookupJob;
use phpDocumentor\Reflection\Utils;

/**
 * Base service for SMS sending functionality
 *
 * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest
 */
class BaseSmsService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function make():self
    {
        return new static();
    }

    /**
     * Send SMS message asynchronously via queue
     *
     * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest::it_can_activate_corporate_registration()
     * @param string $normalized_mobile Recipient's mobile number
     * @param string $message SMS message content
     * @param string|null $date Scheduled date (optional)
     * @param string|null $type SMS type (optional)
     * @param string|null $localid Local ID for tracking (optional)
     * @return void
     */
    public function voidSend($normalized_mobile, $message, $date = null, $type = null, $localid = null)
    {
        SmsSendJob::dispatch($normalized_mobile, $message, $date, $type, $localid);
    }

    /**
     * Send SMS to corporate CEO as part of activation process
     *
     * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest::it_can_activate_corporate_registration()
     * @param string $recipient Recipient's mobile number
     * @param string $message SMS message content
     * @return bool Success status
     */
    public function sendSms(string $recipient, string $message): bool
    {
        $this->voidSend($recipient, $message);
        return true;
    }

    public function actSendArray():self
    {

    }

    public function actStatus():self
    {

    }

    public function actStatusLocalMessageId():self
    {

    }

    public function actSelect():self
    {

    }

    public function actSelectOutbox():self
    {

    }

    public function voidVerifyLookup( $receptor,
    $token,
    $token2,
    $token3,
    $template,
    $type=null)
    {
        SmsVerifyLookupJob::dispatch($receptor,
        $token,
        $token2,
        $token3,
        $template,
        $type);
    }

    public function actCallMakeTTS():self
    {

    }
}
