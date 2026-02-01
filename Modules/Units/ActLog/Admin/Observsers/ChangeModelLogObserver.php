<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/19/25, 7:40 PM
 */

namespace Units\ActLog\Admin\Observsers;

use Modules\Basic\Observers\BaseModelChangeLogObserver;
use Modules\Basic\Services\BaseActLogService;
use Units\ActLog\Admin\Services\ActLogService;


class ChangeModelLogObserver extends BaseModelChangeLogObserver
{
    protected BaseActLogService|string $actLogService=ActLogService::class;
}
