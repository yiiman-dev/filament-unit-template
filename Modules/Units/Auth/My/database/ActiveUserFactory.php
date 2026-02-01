<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/7/25, 5:08 PM
 */

namespace Units\Auth\My\database;

use Illuminate\Database\Eloquent\Factories\Factory;
use Units\Auth\My\Enums\UserStatusEnum;
use Units\Auth\My\Models\UserModel;
use Units\Users\Manage\My\Enums\ValidateStatusEnum;

class ActiveUserFactory extends Factory
{
    protected $model = UserModel::class;

    /**
     * @inheritDoc
     */
    public function definition()
    {
        return [
//            'id' => $this->faker->name,
            'national_code' => $this->faker->numerify('##########'),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'status' => UserStatusEnum::ACTIVE->value,
            'validate_status' => ValidateStatusEnum::VALIDATED->value,
            'validate_request_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => date('Y-m-d H:i:s'),
            'created_by' => 'factory',
            'deleted_by' => 'factory',
            'deleted_reason' => ''
        ];
    }
}
