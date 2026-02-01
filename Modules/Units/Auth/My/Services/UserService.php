<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:58 PM
 */

namespace Units\Auth\My\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Basic\BaseKit\BaseService;
use Units\Auth\My\Enums\CreatedByEnum;
use Units\Auth\My\Enums\UserStatusEnum;
use Units\Auth\My\Enums\ValidateStatusEnum;
use Units\Auth\My\Models\UserModel;

class UserService extends BaseService
{
    public function getByNationalCode($national_code): UserModel|null
    {
        return UserModel::where('national_code', $national_code)->first();
    }

    /**
     * @param string $normalized_phone_number like +9893512345678
     * @return UserModel|null
     */
    public function getByMobile(string $normalized_phone_number): UserModel|null
    {
        return UserModel::where('phone_number', $normalized_phone_number)->first();
    }

    /**
     * ایجاد کاربر جدید در پنل من
     * return data:
     * ```
     * [
     *      'user'=> UserModel
     * ]
     * ```
     *
     * @param string $national_code کد ملی کاربر
     * @param string $phone_number شماره موبایل کاربر
     * @param int $status وضعیت کاربر
     * @param int $validate_status وضعیت تأیید کاربر
     * @param string $created_by ایجاد کننده
     * @return self
     */
    public function actCreate(
        string $national_code,
        string $phone_number,
        int $status = null,
        int $validate_status = null,
        string $created_by = null
    ): self {
        // تنظیم مقادیر پیش‌فرض با استفاده از enum
        $status = $status ?? UserStatusEnum::ACTIVE->value;
        $validate_status = $validate_status ?? ValidateStatusEnum::VALIDATED->value;
        $created_by = $created_by ?? CreatedByEnum::SYSTEM->value;

        // بررسی وجود کاربر قبلی
        $existingUser = $this->getByNationalCode($national_code) ?? $this->getByMobile($phone_number);
        if ($existingUser) {
            $this->addError([], 'کاربری با این اطلاعات قبلاً ثبت شده است');
            return $this;
        }

        DB::beginTransaction();
        try {
            // ایجاد کاربر


            $user = UserModel::create([
                'national_code' => $national_code,
                'phone_number' => $phone_number,
                'status' => $status,
                'validate_status' => $validate_status,
                'created_by' => $created_by,
                'deleted_by' => '',
                'deleted_reason' => '',
                'created_at' => now(),
            ]);

            DB::commit();
            $this->setSuccessResponse([
                'user' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError([], 'خطا در ایجاد کاربر: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * ذخیره متادیتا برای کاربر
     *
     * @param string $national_code کد ملی کاربر
     * @param string $meta_key کلید متادیتا
     * @param string $meta_value مقدار متادیتا
     * @param string $created_by ایجاد کننده
     * @return self
     */
    public function actSaveMetadata(
        string $national_code,
        string $meta_key,
        string $meta_value,
        string $created_by = null
    ): self {
        // تنظیم مقدار پیش‌فرض با استفاده از enum
        $created_by = $created_by ?? CreatedByEnum::SYSTEM->value;

        // بررسی وجود کاربر
        $user = $this->getByNationalCode($national_code);
        if (!$user) {
            $this->addError([], 'کاربری با این کد ملی یافت نشد');
            return $this;
        }

        try {
            // حذف متادیتای قبلی با همین کلید (اگر وجود داشته باشد)
            DB::connection('my')->table('user_meta')
                ->where('national_code', $national_code)
                ->where('meta_key', $meta_key)
                ->delete();

            // ایجاد متادیتای جدید
            DB::connection('my')->table('user_meta')->insert([
                'national_code' => $national_code,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'created_by' => $created_by,
                'id' => Str::uuid(),
                'updated_by' => $created_by,
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_by' => '',
                'deleted_reason' => '',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->setSuccessResponse();
        } catch (\Exception $e) {
            $this->addError([], 'خطا در ذخیره متادیتا: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * ذخیره رمز عبور برای کاربر
     *
     * @param string $national_code کد ملی کاربر
     * @param string $password رمز عبور (به صورت متن ساده)
     * @param string $created_by ایجاد کننده
     * @return self
     */
    public function actSetPassword(
        string $national_code,
        string $password,
        string $created_by = null
    ): self {
        $created_by = $created_by ?? CreatedByEnum::SYSTEM->value;

        return $this->actSaveMetadata(
            $national_code,
            'password_hash',
            Hash::make($password),
            $created_by
        );
    }

    /**
     * ذخیره نام کاربر
     *
     * @param string $national_code کد ملی کاربر
     * @param string $first_name نام کاربر
     * @param string $last_name نام کاربر
     * @param string $created_by ایجاد کننده
     * @return self
     */
    public function actSetName(
        string $national_code,
        string $first_name,
        string $last_name,
        string $created_by = null
    ): self {
        $created_by = $created_by ?? CreatedByEnum::SYSTEM->value;

        $out= $this->actSaveMetadata(
            $national_code,
            'first_name',
            $first_name,
            $created_by
        );

        $out= $this->actSaveMetadata(
            $national_code,
            'last_name',
            $last_name,
            $created_by
        );
        return $out;
    }
}
