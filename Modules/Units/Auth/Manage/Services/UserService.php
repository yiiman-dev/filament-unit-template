<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:57 PM
 */

namespace Units\Auth\Manage\Services;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\Helpers\Helper;
use Units\Auth\Manage\Models\UserModel;
use Units\Auth\My\Models\UserModel as MyUserModel;
use Units\Users\Manage\My\Models\UserMeta;

/**
 * Class UserService
 * @package Modules\FilamentAdmin\Services\V1
 *
 * Service for managing user accounts in the admin panel.
 *
 * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest
 */
class UserService extends BaseService
{

    const STATUS_ACTIVE = 1;
    const STATUS_DE_ACTIVE = 2;
    const CREATED_BY_SYSTEM = 'command';

    /**
     * Create a user account
     *
     * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest::it_can_activate_corporate_registration()
     *
     * return data is:
     * ```
     * [
     *      'user'=> UserModel
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
            $user = UserModel::create([
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
     * Create a user for CEO of corporate registration
     *
     * A convenience method for creating corporate CEO users
     *
     * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest::it_can_activate_corporate_registration()
     * @param array $userData User data including name, phone_number, national_code, password
     * @return array Created user data
     */
    public function createUser(array $userData): array
    {
        $this->actCreate(
            $userData['national_code'] ?? '',
            $userData['password'] ?? '',
            $userData['phone_number'] ?? '',
            self::STATUS_ACTIVE,
            self::CREATED_BY_SYSTEM
        );

        if ($this->hasErrors()) {
            return [];
        }

        return $this->getSuccessResponse()['user']->toArray();
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
        try {
            $user = UserModel::where('phone_number', $normalized_mobile)->first();

            if (!$user) {
                $this->addError([], 'کاربر یافت نشد');
                return $this;
            }

            $user->password_hash = Hash::make($new_password);
            $user->save();

            $this->setSuccessResponse();

        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            $this->addError([], 'خطا در تغییر رمز عبور');
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

    public function getByMobile($mobile): UserModel|null
    {
        return UserModel::where('phone_number', $mobile)->first();
    }


    public function getByUserName($user_name): UserModel|null
    {
        return UserModel::where('username', $user_name)->first();
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
        if (!empty($child)) {
            if ($child->created_by == $parent_phone_number) {
                return true;
            } else {
                return $this->isParent($child->created_by,$parent_phone_number);
            }
        } else {
            return false;
        }
    }

    /**
     * جستجوی کاربران بر اساس شماره موبایل، کد ملی، نام و نام خانوادگی
     *
     * این متد کاربران را از دیتابیس My جستجو می‌کند (برای استفاده در افزودن کاربر به شرکت)
     *
     * return data:
     * ```
     * [
     *     'users' => [
     *         (object)[
     *             'id' => int,
     *             'mobile' => string,
     *             'name' => string
     *         ]
     *     ]
     * ]
     * ```
     *
     * @param string $search متن جستجو
     * @return self
     */
    public function actSearchUsers(string $search): self
    {
        try {
            $normalizedSearch = Helper::normalize_phone_number($search);

            // جستجو در کاربران My (چون CorporateUsersModel به My UserModel وابسته است)
            $users = MyUserModel::leftJoin('user_meta', 'user_meta.national_code','=','users.national_code')->where(function ($query) use ($search, $normalizedSearch) {
                // جستجو بر اساس شماره موبایل
                if (!empty($normalizedSearch)) {
                    $query->where('users.phone_number', 'like', "%{$normalizedSearch}%");
                }

                // جستجو بر اساس کد ملی
                $query->orWhere('users.national_code', 'like', "%{$search}%");
                $query->orWhere('user_meta.meta_value', 'like', "%{$search}%");
            })
            ->limit(50)
            ->get();

            // تبدیل به فرمت مورد نیاز
            $formattedUsers = $users->map(function (MyUserModel $user) {
                // استفاده از __get magic method که خودش metadata را واکشی می‌کند
                $firstName = $user->getMeta('first_name') ?? '';
                $lastName = $user->getMeta('last_name') ?? '';
                $fullName = trim($firstName . ' ' . $lastName) ?: 'بدون نام';

                return (object)[
                    'id' => $user->id,
                    'mobile' => $user->phone_number,
                    'name' => $fullName
                ];
            });

            $this->setSuccessResponse([
                'users' => $formattedUsers
            ]);
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            $this->addError([], 'خطا در جستجوی کاربران');
        }

        return $this;
    }

    /**
     * دریافت کاربر بر اساس شناسه
     *
     * این متد کاربر را از دیتابیس My دریافت می‌کند (برای استفاده در افزودن کاربر به شرکت)
     *
     * return data:
     * ```
     * [
     *     'user' => (object)[
     *         'id' => int,
     *         'mobile' => string,
     *         'name' => string
     *     ]
     * ]
     * ```
     *
     * @param int $userId شناسه کاربر
     * @return self
     */
    public function actGetUserById(int $userId): self
    {
        try {
            // جستجو در کاربران My (چون CorporateUsersModel به My UserModel وابسته است)
            $user = MyUserModel::find($userId);

            if (empty($user)) {
                $this->addError([], 'کاربر یافت نشد');
                return $this;
            }

            // استفاده از __get magic method که خودش metadata را واکشی می‌کند
            $firstName = $user->first_name ?? '';
            $lastName = $user->last_name ?? '';
            $fullName = trim($firstName . ' ' . $lastName) ?: 'بدون نام';

            $this->setSuccessResponse([
                'user' => (object)[
                    'id' => $user->id,
                    'mobile' => $user->phone_number,
                    'name' => $fullName
                ]
            ]);
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            $this->addError([], 'خطا در دریافت اطلاعات کاربر');
        }

        return $this;
    }
}
