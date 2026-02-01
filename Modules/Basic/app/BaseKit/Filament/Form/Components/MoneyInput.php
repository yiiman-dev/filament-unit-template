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

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Basic\Helpers\Helper;

/**
 * MoneyInput
 *
 * کامپوننت ورودی مبلغ با فرمت پولی
 * این کامپوننت مبالغ را با فرمت دلار ($) دریافت می‌کند
 * و از ماسک JavaScript برای اعتبارسنجی استفاده می‌کند
 *
 * @see MoneyInputTest
 */
class MoneyInput extends TextInput
{
    /**
     * تنظیمات اولیه کامپوننت
     * شامل ماسک ورودی، حذف کاراکترهای خاص و ویژگی‌های اضافی
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->mask(RawJs::make('$money($input)'))
            ->stripCharacters(',')
            ->numeric()
            ->extraInputAttributes([
                'inputmode' => 'numeric',
                'dir' => 'ltr',
            ]);
    }
}
