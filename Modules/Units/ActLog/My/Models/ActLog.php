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

namespace Units\ActLog\My\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Basic\Models\BaseActLog;
use Units\Auth\My\Models\UserModel;

class ActLog extends BaseActLog
{

    protected $connection = 'my';

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'phone_number', 'phone_number');
    }
}
