<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:58 PM
 */

namespace Units\Auth\My\Services;

use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\Helpers\Helper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\SMS\Common\Services\BaseSmsService;

/**
 * Debug version of AuthService to help identify session persistence issues
 */
class DebugAuthService extends BaseService
{
    private $_verify_key = '_verify';

    /**
     * @param $mobile string Tell me normalized mobile number, like 09353466620
     * @return void
     */
    public function actSendOTP(string $mobile): self
    {
        if (empty($mobile)){
            throw new \Exception('شماره همراه وارد نشده است');
        }
        $mobile=$this->normalize_mobile($mobile);

        // Debug session information
        \Illuminate\Support\Facades\Log::info('DEBUG: actSendOTP - Session ID before: ' . session()->getId());
        \Illuminate\Support\Facades\Log::info('DEBUG: actSendOTP - Session data before: ' . json_encode(session()->all()));

        session()->regenerate();
        \Illuminate\Support\Facades\Log::info('DEBUG: actSendOTP - Session ID after regenerate: ' . session()->getId());

        $conf=config('session');
        cache()->put(
            $this->_generate_private_key($mobile),
            $code=rand(111111, 999999),
            $this->_get_otp_time()
        );

        \Illuminate\Support\Facades\Log::info('OTP code for login for mobile '.$mobile.' is '.$code);
        session()->put('state', 'verify');
        session()->put('my_mobile_phone', $mobile);

        // Debug session information after setting
        \Illuminate\Support\Facades\Log::info('DEBUG: actSendOTP - Session ID after set: ' . session()->getId());
        \Illuminate\Support\Facades\Log::info('DEBUG: actSendOTP - my_mobile_phone in session: ' . session()->get('my_mobile_phone', 'NOT_SET'));
        \Illuminate\Support\Facades\Log::info('DEBUG: actSendOTP - Session data after set: ' . json_encode(session()->all()));

        app(BaseSmsService::class)->voidVerifyLookup($mobile, $code, null, null, 'verify');
        $this->setSuccessResponse();
        return $this;
    }

    /**
     * Get mobile from session with debugging
     * @return string
     */
    public function getMobileNumber():string
    {
        try {
            $mobile = session()->get('my_mobile_phone');
            \Illuminate\Support\Facades\Log::info('DEBUG: getMobileNumber - Retrieved mobile: ' . $mobile);
            \Illuminate\Support\Facades\Log::info('DEBUG: getMobileNumber - Session ID: ' . session()->getId());
            \Illuminate\Support\Facades\Log::info('DEBUG: getMobileNumber - Session data: ' . json_encode(session()->all()));

            return (string)$mobile;
        }catch (\Exception|ContainerExceptionInterface|NotFoundExceptionInterface $e){
            \Illuminate\Support\Facades\Log::error('DEBUG: getMobileNumber - Exception occurred: ' . $e->getMessage());
            $this->addError(['exception'=>$e->getMessage()],'سشن کرش کرد');
            return '';
        }
    }

    /**
     * Debug method to check session state
     */
    public function debugSessionState(): array
    {
        return [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'my_mobile_phone' => session()->get('my_mobile_phone', 'NOT_FOUND'),
            'state' => session()->get('state', 'NOT_SET'),
        ];
    }

    private function _generate_private_key(string $mobile): string
    {
        return $mobile . $this->_verify_key;
    }

    private function _get_otp_time(): int
    {
        return 120;
    }

    /**
     *
     * @param $mobile
     * @return string
     */
    private function normalize_mobile($mobile):string
    {
        return Helper::normalize_phone_number($mobile);
    }

    // Other methods remain the same...
}
