<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:57 PM
 */

namespace Units\Auth\Manage\Services;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\Hash;
use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\Helpers\Helper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\Auth\Manage\Models\UserModel;
use Units\SMS\Common\Services\BaseSmsService;

/**
 * #11.
 */
class AuthService extends BaseService
{
    private $_verify_key = '_verify_manage';

    /**
     * @param  $mobile  string Tell me normalized mobile number, like 09353466620
     * @return void
     */
    public function actSendOTP(string $mobile): self
    {
        $mobile = Helper::normalize_phone_number($mobile);
        session()->regenerate();
        cache()->put(
            $this->_generate_private_key($mobile),
            $code = rand(111111, 999999),
            $this->_get_otp_time()
        );
        $this->logInfo('Admin user login OTP: '.$code);
        app(BaseSmsService::class)->voidVerifyLookup($mobile, $code, null, null, 'verify');
        session()->put('state', 'verify');
        session()->put('manage_mobile_phone', $mobile);
        $this->setSuccessResponse();

        return $this;
    }

    public function actValidate(string $username, string $password, string $mobile): self
    {

        $mobile = Helper::normalize_phone_number($mobile);
        $user = UserModel::where(['phone_number' => $mobile])->first();

        if (empty($user)) {
            $this->addError([], 'نام کاربری یا رمز عبور اشتباه است');

            return $this;
        }
        if (empty($username) || empty($password)) {
            $this->addError([], 'نام کاربری یا رمز عبور اشتباه است');

            return $this;
        }

        if ($user->username !== $username) {
            $this->addError([], 'نام کاربری یا رمز عبور اشتباه است');

            return $this;
        }
        if (! Hash::check($password, $user->password_hash)) {
            $this->addError([], 'نام کاربری یا رمز عبور اشتباه است');

            return $this;
        }
        if ($user->status != UserService::STATUS_ACTIVE) {
            $this->addError([], 'کاربری شما مسدود شده است');

            return $this;
        }
        $this->setSuccessResponse();

        return $this;
    }

    /**
     * @param  $mobile  string normalized mobile
     * @return mixed|object|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getOTPVerifyCode($mobile)
    {
        return cache()->get($this->_generate_private_key($mobile));

    }

    /**
     * Return data is empty and success response is void
     * Verify given code with code that saved on cache
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function actVerify(string $code): self
    {
        $code = str_replace(['_', '-', ' '], '', $code);

        $mobile = $this->getMobileNumber();
        if (empty($mobile)) {
            $this->addError([], 'شماره همراه');

            return $this;
        }

        $cacheCode = $this->getOTPVerifyCode($mobile);

        switch (true) {
            case $code == $cacheCode:
                // Check user is not banned
                $userModel = resolve(UserService::class)->getByMobile($mobile);
                if ($userModel->status != UserService::STATUS_ACTIVE) {
                    $this->addError([], 'کاربری شما مسدود شده است');

                    return $this;
                }
                $this->setSuccessResponse();
                break;
            case empty($code):
            default:
                $this->addError([], 'کد وارد شده اشتباه است');
                break;
        }

        return $this;
    }

    /**
     * return data:
     * ```
     *  [
     *      'mobile'=>'string'
     *  ]
     *
     * ```
     *
     * @return string
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getMobileNumber()
    {
        $response = session()->get('manage_mobile_phone');
        if ($response) {
            return $response;
        } else {
            return null;
        }

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function hasMobileOnCache(): bool
    {
        return ! empty($this->getMobileNumber());
    }

    public function doif_hasMobileOnCache(bool $condition, callable $function): mixed
    {
        if ($condition) {
            return $function();
        }

        return null;
    }

    /**
     * success data:
     * ```
     * [
     *      'user'=>UserModel
     * ]
     *
     * ```
     *
     * @return $this
     */
    public function actSetLoggedInUser($mobile): self
    {
        $user = UserModel::where('phone_number', $mobile)->first();
        if (empty($user)) {
            $this->addError([], 'کاربری با این شماره همراه یافت نشد');

            return $this;
        }
        Filament::getPanel('manage')->auth()->login($user);
        Filament::getPanel('manage')->auth()->setUser($user);
        $user = Filament::getPanel('manage')->auth()->user();
        $check = Filament::getPanel('manage')->auth()->check();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::getPanel('manage')->auth()->logout();

            $this->addError();
        }

        // < Unique session >

        request()->session()->regenerate();

        $userID = Filament::getPanel('manage')->auth()->user()->phone_number;
        $roles = $user->roles->pluck('name');
        $permissions = $user->permissions;
        $deptID = $user->deptID;
        $name = ' - - ';

        session(['rol' => $roles]);
        session(['permissions' => $permissions]);
        session(['id' => $userID]);
        session(['departamento' => $deptID]);
        session(['fullName' => $name]);

        // </ Unique session >

        $this->setSuccessResponse([
            'user' => $user,
        ]);

        return $this;
    }

    private function _generate_private_key(string $mobile): string
    {
        return $mobile.$this->_verify_key;
    }

    private function _get_otp_time(): int
    {
        return 120;
    }
}
