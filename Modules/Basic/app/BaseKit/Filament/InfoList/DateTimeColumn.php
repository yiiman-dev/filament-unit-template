<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/19/25, 6:56 PM
 */

namespace Modules\Basic\BaseKit\Filament\InfoList;

use Filament\Tables\Columns\TextColumn;
use Modules\Basic\Services\BaseActLogService;
use YiiMan\functions\functions;

class DateTimeColumn extends TextColumn
{
    public function toJalali():self
    {
        $f=resolve(functions::class);
        $this->formatStateUsing(fn (string $state) => $f->convert_dateTime($state));
        return $this;
    }
}
