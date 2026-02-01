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

namespace Units\Auth\My\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Basic\BaseKit\BaseService;
use Units\Auth\My\Models\UserMetadata;
use Units\Auth\My\Models\UserModel;

class UserMetadataService extends BaseService
{
    /**
     * به‌روزرسانی اطلاعات پروفایل کاربر
     *
     * @param string $national_code
     * @param array $data
     * @return $this
     */
    public function actUpdateProfile(string $national_code, array $data): self
    {
        try {
            UserMetadata::updateProfile($national_code, $data);

            $this->setSuccessResponse();
        } catch (\Exception $e) {
            $this->addError($e->getTrace(), $e->getMessage());
        }

        return $this;
    }

    /**
     * دریافت اطلاعات پروفایل کاربر
     *
     * @param string $national_code
     * @return array
     */
    public function getProfile(string $national_code): array
    {
        return UserMetadata::getProfile($national_code);
    }

    /**
     * مدیریت آپلود تصویر پروفایل
     *
     * @param mixed $image
     * @param string $normalized_phone_number
     * @return string
     */
    private function handleProfileImage($image, string $normalized_phone_number): string
    {
        $path = "profile_images/{$normalized_phone_number}";
        $filename = time() . '.' . $image->getUploadedFiles();

        Storage::disk('public')->putFileAs($path, $image, $filename);

        return "{$path}/{$filename}";
    }

    public function get_meta_key(string $meta_key, string $national_code = ''): string|null
    {
        if (empty($national_code)) {
            $national_code = auth()?->user()?->national_code;
        }

        return UserMetadata::where('national_code', $national_code)
            ->where('meta_key', $meta_key)
            ->first()?->meta_value;
    }

    public function get_name(string $national_code = '')
    {
        return $this->get_meta_key('first_name', $national_code);
    }


    public function add_if_not_exists(string $meta_key, string|null $meta_value, string $national_code = ''):void
    {
        if (empty($national_code)) {
            $national_code = auth()?->user()?->national_code;
        }
        $user_meta = UserMetadata::where('national_code', $national_code)
            ->where('meta_key', $meta_key)->first();
        if (!empty($user_meta)){
            return;
        }
        UserMetadata::create([
            'id' => Str::uuid(),
            'national_code' => $national_code,
            'meta_key' => $meta_key,
            'meta_value' => (string)$meta_value,
        ]);
    }

    public function add_meta_key(string $meta_key, string|null $meta_value, string $national_code = '')
    {
        return UserMetadata::add_meta_key($meta_key, $meta_value);
    }
}
