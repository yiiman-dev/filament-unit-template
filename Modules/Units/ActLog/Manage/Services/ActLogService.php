<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/26/25, 5:53 PM
 */

namespace Units\ActLog\Manage\Services;


use Modules\Basic\Services\BaseActLogService;
use Units\ActLog\Manage\Models\ActLog;

/**
 *
 * این سرویس برای ثت فعالیت های اپلیکیشن استفاده می شود
 */
class ActLogService extends BaseActLogService
{
    protected static string $model = ActLog::class;


}
