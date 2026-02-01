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

namespace Units\Users\Manage\My\DTOs;

use Carbon\Carbon;
use Units\Users\Manage\My\Enums\UserStatusEnum;
use Units\Users\Manage\My\Enums\ValidateStatusEnum;

class UserDTO
{
    public function __construct(
        public readonly string $national_code,
        public readonly string $phone_number,
        public readonly UserStatusEnum $status = UserStatusEnum::ACTIVE,
        public readonly ValidateStatusEnum $validate_status = ValidateStatusEnum::NOT_VALIDATED,
        public readonly ?Carbon $validate_request_at = null,
        public readonly string $created_by = 'system',
        public readonly ?string $deleted_by = null,
        public readonly ?string $deleted_reason = null,
        public readonly ?int $id = null,
        public readonly ?Carbon $created_at = null,
        public readonly ?Carbon $updated_at = null,
        public readonly ?Carbon $deleted_at = null,
    ) {
    }
    
    /**
     * Create a UserDTO from an array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            national_code: $data['national_code'],
            phone_number: $data['phone_number'],
            status: isset($data['status']) 
                ? (is_int($data['status']) 
                    ? ($data['status'] === UserStatusEnum::ACTIVE->value 
                        ? UserStatusEnum::ACTIVE 
                        : UserStatusEnum::INACTIVE) 
                    : $data['status'])
                : UserStatusEnum::ACTIVE,
            validate_status: isset($data['validate_status']) 
                ? (is_int($data['validate_status']) 
                    ? ($data['validate_status'] === ValidateStatusEnum::VALIDATED->value 
                        ? ValidateStatusEnum::VALIDATED 
                        : ValidateStatusEnum::NOT_VALIDATED) 
                    : $data['validate_status'])
                : ValidateStatusEnum::NOT_VALIDATED,
            validate_request_at: isset($data['validate_request_at']) 
                ? (is_string($data['validate_request_at']) 
                    ? Carbon::parse($data['validate_request_at']) 
                    : $data['validate_request_at'])
                : null,
            created_by: $data['created_by'] ?? 'system',
            deleted_by: $data['deleted_by'] ?? null,
            deleted_reason: $data['deleted_reason'] ?? null,
            id: $data['id'] ?? null,
            created_at: isset($data['created_at']) 
                ? (is_string($data['created_at']) 
                    ? Carbon::parse($data['created_at']) 
                    : $data['created_at'])
                : null,
            updated_at: isset($data['updated_at']) 
                ? (is_string($data['updated_at']) 
                    ? Carbon::parse($data['updated_at']) 
                    : $data['updated_at'])
                : null,
            deleted_at: isset($data['deleted_at']) 
                ? (is_string($data['deleted_at']) 
                    ? Carbon::parse($data['deleted_at']) 
                    : $data['deleted_at'])
                : null,
        );
    }
    
    /**
     * Convert UserDTO to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'national_code' => $this->national_code,
            'phone_number' => $this->phone_number,
            'status' => $this->status->value,
            'validate_status' => $this->validate_status->value,
            'validate_request_at' => $this->validate_request_at,
            'created_by' => $this->created_by,
            'deleted_by' => $this->deleted_by,
            'deleted_reason' => $this->deleted_reason,
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
} 