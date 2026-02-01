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

use Illuminate\Support\Facades\Storage;
use Modules\Basic\BaseKit\BaseService;
use Units\Auth\Manage\Models\UserMetadata;

class UserMetadataService extends BaseService
{
    /**
     * به‌روزرسانی اطلاعات پروفایل کاربر
     *
     * @param string $phone_number
     * @param array $data
     * @return $this
     */
    public function actUpdateProfile(string $phone_number, array $data): self
    {
        try {
            \Units\Auth\Manage\Models\UserMetadata::updateProfile($phone_number, $data);

            $this->setSuccessResponse();
        } catch (\Exception $e) {
            $this->addError($e->getTrace(), $e->getMessage());
        }

        return $this;
    }

    /**
     * دریافت اطلاعات پروفایل کاربر
     *
     * @param string $normalized_phone_number
     * @return $this
     */
    public function getProfile(string $normalized_phone_number):array
    {
        return UserMetadata::getProfile($normalized_phone_number);

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
}
