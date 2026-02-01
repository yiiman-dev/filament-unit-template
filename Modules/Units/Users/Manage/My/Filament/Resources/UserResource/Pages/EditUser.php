<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Units\Users\Manage\My\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Units\Auth\My\Models\UserMetadata;
use Units\Auth\My\Models\UserModel;
use Units\Users\Manage\My\DTOs\UserDTO;
use Units\Users\Manage\My\Enums\UserStatusEnum;
use Units\Users\Manage\My\Enums\ValidateStatusEnum;
use Units\Users\Manage\My\Filament\Resources\UserResource;
use Units\Users\Manage\My\Services\UserService;

/**
 * @property UserModel $record
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('حذف')
                ->before(function () {
                    // This prevents the default delete behavior as we handle it in the resource
                    $this->cancel();
                }),
            Actions\RestoreAction::make()
                ->label('بازیابی'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Convert status to enum
        $status = isset($data['status']) ?
            ($data['status'] == UserStatusEnum::ACTIVE->value ? UserStatusEnum::ACTIVE : UserStatusEnum::INACTIVE) :
            UserStatusEnum::ACTIVE;

        // Convert validate_status to enum
        $validateStatus = isset($data['validate_status']) ?
            ($data['validate_status'] == ValidateStatusEnum::VALIDATED->value ? ValidateStatusEnum::VALIDATED : ValidateStatusEnum::NOT_VALIDATED) :
            ValidateStatusEnum::NOT_VALIDATED;

        // Create DTO
        $userDTO = new UserDTO(
            national_code: $data['national_code'],
            phone_number: $data['phone_number'],
            status: $status,
            validate_status: $validateStatus,
            id: $record->id,
        );

        // Call service
        $userService = new UserService();
        $userService->actUpdateUser($record->id, $userDTO);

        if ($userService->hasErrors()) {
            $this->halt();

            foreach ($userService->getErrorMessages() as $message) {
                $this->notify('danger', $message);
            }

            return $record;
        }

        $response = $userService->getSuccessResponse();

        return $response->getData('user');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'اطلاعات کاربر با موفقیت ذخیره شد';
    }

    public function afterSave()
    {
        UserMetadata::updateProfile($this->record->national_code, [
            'first_name' => $this->data['first_name'],
            'last_name' => $this->data['last_name'],
            'birth_day' => $this->data['birth_day'],
            'bio' => $this->data['bio'],
            'address' => $this->data['address'],
            'bank_account_payment_card_no' => $this->data['bank_account_payment_card_no'],
            'bank_account_sheba' => $this->data['bank_account_sheba'],
        ]);
    }
}
