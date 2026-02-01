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
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Units\Users\Manage\My\DTOs\UserDTO;
use Units\Users\Manage\My\Enums\UserStatusEnum;
use Units\Users\Manage\My\Enums\ValidateStatusEnum;
use Units\Users\Manage\My\Filament\Resources\UserResource;
use Units\Users\Manage\My\Services\UserService;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->name ?? 'سیستم';
        return $data;
    }
    
    protected function handleRecordCreation(array $data): Model
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
            created_by: $data['created_by'] ?? 'سیستم',
        );
        
        // Call service
        $userService = new UserService();
        $userService->actCreateUser($userDTO);
        
        if ($userService->hasErrors()) {
            $this->halt();
            
            foreach ($userService->getErrorMessages() as $message) {
                $this->notify('danger', $message);
            }
            
            return null;
        }
        
        $response = $userService->getSuccessResponse();
        
        return $response->user;
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'کاربر با موفقیت ایجاد شد';
    }
} 