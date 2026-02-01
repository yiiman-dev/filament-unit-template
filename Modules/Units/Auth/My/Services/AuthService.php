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

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\Helpers\Helper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\Auth\My\Models\UserModel;
use Units\SMS\Common\Services\BaseSmsService;

/**
 * #11.
 *
 */
class AuthService extends BaseService
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
        session()->regenerate();

        cache()->put(
            $this->_generate_private_key($mobile),
            $code=rand(111111, 999999),
            $this->_get_otp_time()
        );

        \Illuminate\Support\Facades\Log::info('OTP code for login for mobile '.$mobile.' is '.$code);
        session()->put('state', 'verify');
        session()->put('my_mobile_phone', $mobile);
        app(BaseSmsService::class)->voidVerifyLookup($mobile, $code, null, null, 'verify');
        $this->setSuccessResponse();
        return $this;
    }



    /**
     * این متد برای بررسی اینکه آیا زمان اعتبار کد وارد شده از طریق پیامک به اتمام رسیده است یا خیر استفاده می شود
     * @return bool
     */
    public function hasVerifyTime():bool
    {

        return cache()->has($this->_generate_private_key((string)session()->get('my_mobile_phone')));
    }

    /**
     * این متد برای اجرای یک تابع در صورتی که زمان اعتبار کد وارد شده از طریق پیامک به اتمام نرسیده است استفاده می شود
     * @param callable $function
     * @return void
     */
    public function doif_hasVerifyTime(callable $function):void
    {
        if ($this->hasVerifyTime()){
            $function();
        }
    }

    /**
     * این متد برای اجرای یک تابع در صورتی که زمان اعتبار کد وارد شده از طریق پیامک به اتمام رسیده است استفاده می شود
     * @param callable $function
     * @return void
     */
    public function doif_has_notVerifyTime(callable $function):void
    {
        if (!$this->hasVerifyTime()){
            $function();
        }
    }

    /**
     * Return data is empty and success response is void
     * Verify given code with code that saved on cache
     *
     * Errors:
     *
     * ```
     * ['error_type'=>'invalid_mobile'] => Should redirect to login page
     * ['error_type'=>'invalid_code'] => Should refresh verify page
     *
     * ```
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function actVerify(string $code): self
    {
        $code=trim($code);
        $code=str_replace(' ','',$code);
        $code=str_replace('-','',$code);
        $code=str_replace('_','',$code);
        $code=str_replace('+','',$code);
        $code=str_replace('*','',$code);

        $mobile=$this->getMobileNumber();
        if (empty($mobile)){
            $this->addError(['error_type'=>'invalid_mobile'], 'لطفا شماره همراه خود را وارد کنید');
            return $this;
        }

        $cacheCode = cache()->get($this->_generate_private_key($mobile));

        switch (true) {
            case $code == $cacheCode:
                session()->put('state','register');
                $this->refreshService();
                $this->setSuccessResponse();
                break;
            case empty($code):
            default:
                $this->addError(['error_type'=>'invalid_code'], 'کد وارد شده اشتباه است');
                break;
        }

        return  $this;
    }

    public function getVerificationCode()
    {
        $mobile=$this->getMobileNumber();
        return cache()->get($this->_generate_private_key($mobile));
    }


    /**
     * Get mobile from session
     * @return string
     */
    public function getMobileNumber():string
    {
        try {
            return (string)session()->get('my_mobile_phone');
        }catch (\Exception|ContainerExceptionInterface|NotFoundExceptionInterface $e){
            $this->addError(['exception'=>$e->getMessage()],'سشن کرش کرد');
            return '';
        }
    }

    /**
     * در صورتی که کاربر در حال ثبت نام است و شماره همراه خود را وارد کرده است مقدار صحیح بر میگرداند
     * @return bool
     */
    public function isRegistering():bool
    {
        return (session()->has('state') && session()->get('state') == 'register') && session()->has('my_mobile_phone');
    }

    /**
     * در صورتی که کاربر در حال ثبت نام نباشد این متد اجرا می شود
     * @param callable $function
     * @return void
     */
    public function doif_is_not_registering(callable $function):void
    {
        if (!$this->isRegistering()){
            $function();
        }
    }


    /**
     * success data:
     * ```
     * [
     *      'user'=>UserModel
     * ]
     *
     * ```
     * @param $mobile
     * @return $this
     */
    public function actSetLoggedInUser($mobile):self
    {
        $mobile=$this->normalize_mobile($mobile);
        $user=UserModel::where('phone_number',$mobile)->first();
        if (empty($user)){
            $this->addError([],'کاربری با این شماره همراه یافت نشد');
            return $this;
        }

        Filament::getPanel('my')->auth()->login($user);
        Filament::getPanel('my')->auth()->setUser($user);
        $user = Filament::getPanel('my')->auth()->user();
        $check = Filament::getPanel('my')->auth()->check();

        if (
            ($user instanceof FilamentUser) &&
            (!$user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::getPanel('my')->auth()->logout();

            $this->addError();
        }
        $this->setSuccessResponse([
            'user' => $user
        ]);
        return  $this;
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

    public function getUserId()
    {
        return Filament::auth()->user()->id;
    }

    public function logoutUser()
    {
        Filament::auth()->logout();
//        session()->invalidate();
//        session()->regenerateToken();
    }
}
