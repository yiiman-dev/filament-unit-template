<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/8/25, 2:33 AM
 */

namespace Modules\Basic\Concerns;

use Illuminate\Support\Facades\Log;

trait InteractWithLog
{

    protected function logInfo(string $message): void
    {
        Log::info($message);
    }

    protected function logError(string $message): void
    {
        Log::error($message);
    }

    protected function logWarning(string $message): void
    {
        Log::warning($message);
    }

}
