<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 6:11 PM
 */

namespace Units\Users\Manage\Manage\Services;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\Helpers\Helper;
use Units\Users\Manage\Manage\Models\User;

/**
 * Class UserService
 * @package Modules\FilamentAdmin\Services\V1
 *
 */
class UserService extends BaseService
{

    const STATUS_ACTIVE = 1;
    const STATUS_DE_ACTIVE = 2;
    const CREATED_BY_SYSTEM = 'command';

    /**
     * این متد برای ثبت نام کاربر است
     * return data is:
     * ```
     * [
     *      'user'=> User
     * ]
     *
     * ```
     *
     * @param string $username
     * @param string $password
     * @param string $de_normalized_mobile
     * @param int $status
     * @param string $created_by
     * @return self
     */
    public function actCreate(
        string $username,
        string $password,
        string $de_normalized_mobile,
        int    $status,
        string $created_by,
    ): self
    {


        try {
            $de_normalized_mobile = Helper::normalize_phone_number($de_normalized_mobile);
            $user = User::create([
                'username' => $username,
                'password_hash' => Hash::make($password),
                'phone_number' => $de_normalized_mobile,
                'status' => $status,
                'created_by' => $created_by,
                'updated_by' => $created_by,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            $this->logError($e->getMessage());
        }

        if (empty($user)) {
            $this->addError([], 'مشکلی برای ثبت کاربر پیش آمد');
            return $this;
        }
        $this->setSuccessResponse([
            'user' => $user
        ]);
        return $this;
    }

    /**
     * تغییر رمز عبور کاربر بدون نیاز به تأیید رمز فعلی
     *
     * این متد رمز عبور کاربر را مستقیماً تغییر می‌دهد (مثلاً توسط ادمین)
     *
     * @param string $normalized_mobile شماره موبایل نرمالیزه شده کاربر
     * @param string $new_password رمز عبور جدید کاربر (plain text)
     * @return $this
     */
    public function actChangePassword(
        string $normalized_mobile,
        string $new_password
    ): self
    {
        // یافتن کاربر با شماره موبایل
        $user = $this->getByMobile($normalized_mobile);
        if (empty($user)) {
            $this->addError([], 'کاربری با این شماره همراه یافت نشد');
            return $this;
        }

        try {
            // بروزرسانی رمز عبور جدید
            $user->password_hash = Hash::make($new_password);
            $user->updated_by = Filament::getCurrentPanel()->auth()->user()->phone_number;
            $user->save();

            // ثبت لاگ
            Log::info(
                'رمز عبور کاربر: ' . $user->phone_number .
                ' توسط ادمین: ' . Filament::getCurrentPanel()->auth()->user()->phone_number . ' تغییر یافت.'
            );

            $this->setSuccessResponse();
        } catch (\Exception $e) {
            $this->addError($e->getTrace(), $e->getMessage());
            Log::error('خطا در تغییر رمز عبور: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * تغییر رمز عبور کاربر
     *
     * این متد رمز عبور کاربر را پس از تأیید رمز فعلی تغییر میدهد
     *
     * @param string $normalized_mobile شماره موبایل نرمالیزه شده کاربر
     * @param string $current_password رمز عبور فعلی کاربر (پlain text)
     * @param string $new_password رمز عبور جدید کاربر (plain text)
     * @return $this
     */
    public function actChangePasswordConfirmCurrent(
        string $normalized_mobile,
        string $current_password,
        string $new_password
    ): self
    {
        // یافتن کاربر با شماره موبایل
        $user = $this->getByMobile($normalized_mobile);
        if (empty($user)) {
            $this->addError([], 'کاربری با این شماره همراه یافت نشد');
            return $this;
        }

        // بررسی تطابق رمز عبور فعلی
        if (!Hash::check($current_password, $user->password_hash)) {
            $this->addError([], 'رمز عبور فعلی نادرست است');
            return $this;
        }

        try {
            // بروزرسانی رمز عبور جدید
            $user->password_hash = Hash::make($new_password);
            $user->updated_by = Filament::getCurrentPanel()->auth()->user()->phone_number;
            $user->save();

            // ثبت لاگ
            Log::info(
                'تغییر رمز عبور برای کاربر: ' . $user->phone_number .
                ' توسط ادمین: ' . Filament::getCurrentPanel()->auth()->user()->phone_number
            );

            $this->setSuccessResponse();
        } catch (\Exception $e) {
            $this->addError($e->getTrace(), $e->getMessage());
            Log::error('خطا در تغییر رمز عبور: ' . $e->getMessage());
        }

        return $this;
    }

    public function getByMobile($mobile): User|null
    {
        return User::where('phone_number', $mobile)->first();
    }


    public function getByUserName($user_name): User|null
    {
        return User::where('username', $user_name)->first();
    }


    /**
     * غیرفعال سازی کاربر
     *
     * این عمل هیچ نوتیفیکیشنی ارسال نمیکند و برای ارسال نوتیف باید در لایه ی بالاتر عمل کنید
     * @param string $normalized_mobile
     * @param string $reason
     * @return $this
     */
    public function actDeactivate(string $normalized_mobile, string $reason): self
    {
        $userModel = $this->getByMobile($normalized_mobile);
        if (empty($userModel)) {
            $this->addError([], 'کاربری با این شماره همراه یافت نشد');
            return $this;
        }
        $userModel->status = self::STATUS_DE_ACTIVE;
        $userModel->deactivated_reason = $reason;
        try {
            $userModel->save();
            $this->setSuccessResponse();
            Log::info('admin user : ' . $userModel->phone_number . ' Deactivated by Admin User' . Filament::getCurrentPanel()->auth()->user()->phone_number);
        } catch (\Exception $e) {
            $this->addError($e->getTrace(), $e->getMessage());
        }
        return $this;
    }

    /**
     * فعال سازی کاربر ادمین جهت ورود به پنل
     *
     * این تابع هیچ نوتیفیکیشنی ارسال نمیکند و برای ارسال نوتیف باید از لایه ی بالاتر عمل کنید
     * @param string $normalized_mobile
     * @return $this
     */
    public function actActivate(string $normalized_mobile): self
    {
        $userModel = $this->getByMobile($normalized_mobile);
        if (empty($userModel)) {
            $this->addError([], 'کاربری با این شماره همراه یافت نشد');
            return $this;
        }
        $userModel->status = self::STATUS_ACTIVE;
        $userModel->deactivated_reason=null;
        try {
            $userModel->save();
            $this->setSuccessResponse();
            Log::info('admin user : ' . $userModel->phone_number . ' Activated by Admin User' . Filament::getCurrentPanel()->auth()->user()->phone_number);
        } catch (\Exception $e) {
            $this->addError($e->getTrace(), $e->getMessage());
        }
        return $this;
    }

    /**
     * زنجیره ی کاربران ادمین را بررسی میکند تا ببیند آیا کاربری که شماره اش را به عنوان والد احتمالی وارد میکنید٬ واقعا والد هست یا نه
     * @param $child_phone_number
     * @param $parent_phone_number
     * @return bool
     */
    public function isParent($child_phone_number, $parent_phone_number)
    {
        $child = $this->getByMobile($child_phone_number);
        if (!empty($child) && $child->created_by==$child_phone_number){
            return  false;
        }
        if (!empty($child)) {
            $parent_of_child=$child->created_by;
            if ( $parent_of_child== $parent_phone_number) {
                return true;
            } else {
                return $this->isParent($child->created_by,$parent_phone_number);
            }
        } else {
            return false;
        }
    }
}
