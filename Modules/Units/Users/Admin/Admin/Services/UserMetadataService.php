<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:53 PM
 */

namespace Units\Users\Admin\Admin\Services;

use Illuminate\Support\Facades\Storage;
use Modules\Basic\BaseKit\BaseService;
use Units\Auth\Admin\Models\UserMetadata;

class UserMetadataService extends BaseService
{
    /**
     * به‌روزرسانی اطلاعات پروفایل کاربر
     *
     * @param string $normalized_phone_number
     * @param array $data
     * @return $this
     */
    public function actUpdateProfile(string $normalized_phone_number, array $data): self
    {
        try {
            $metadata = UserMetadata::firstOrNew(['phone_number' => $normalized_phone_number]);

            if (isset($data['profile_image'])) {
                $data['profile_image'] = $this->handleProfileImage($data['profile_image'], $normalized_phone_number);
            }

            $metadata->fill($data);
            $metadata->save();

            $this->setSuccessResponse(['metadata' => $metadata]);
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
    public function getProfile(string $normalized_phone_number):null|UserMetadata
    {
        return UserMetadata::where('phone_number', $normalized_phone_number)->first();

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
