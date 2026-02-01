<?php

namespace Units\ActLog\Admin\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Basic\Models\BaseActLog;
use Units\Auth\Admin\Models\UserModel;

class ActLog extends BaseActLog
{

    protected $connection = 'admin';

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
