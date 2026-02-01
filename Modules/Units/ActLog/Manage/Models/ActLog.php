<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/26/25, 5:52 PM
 */

namespace Units\ActLog\Manage\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Basic\Models\BaseActLog;
use Units\Auth\Manage\Models\UserModel;

class ActLog extends BaseActLog
{

    protected $connection = 'manage';

    protected $fillable = [
        'phone_number',
        'action',
        'ip_address',
        'user_agent',
        'details'
    ];

//    protected $casts = [
//        'details' => 'array'
//    ];


}
