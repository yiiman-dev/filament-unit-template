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

namespace Units\Auth\Admin\Services\DTO;

use Modules\Basic\BaseKit\BaseDTO;

/**
 * @property string $username
 * @property string $national_code
 * @property string $phone_number
 * @property int $status
 * @property int $validate_status
 * @property string $password_hash
 * @property string $created_by
 */
class UserRegisterDTO extends BaseDTO
{
    public string $username;
    public string $national_code;
    public string $phone_number;
    public int $status;
    public int $validate_status;
    public string $password_hash;
    public string $created_by;

    public static function make(
        string $username,
        string $national_code,
        string $phone_number,
        int $status,
        int $validate_status,
        string $password_hash,
        string $created_by
    ): self
    {
        $dto = new self();
        $dto->username = $username;
        $dto->national_code = $national_code;
        $dto->phone_number = $phone_number;
        $dto->status = $status;
        $dto->validate_status = $validate_status;
        $dto->password_hash = $password_hash;
        $dto->created_by = $created_by;
        return $dto;
    }
}
