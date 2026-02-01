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

namespace Modules\Basic\BaseKit\Filament\Form\Components;


use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;

/**
 * MoneyColumn
 *
 * کامپوننت ستون جدول برای نمایش مبالغ
 * این کامپوننت مبالغ را با فرمت پولی در جداول نمایش می‌دهد
 *
 * @see MoneyColumnTest
 */
class MoneyColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(fn($state) => number_format($state));
    }
}
