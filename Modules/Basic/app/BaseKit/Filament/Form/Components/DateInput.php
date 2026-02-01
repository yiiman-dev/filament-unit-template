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
 * DateInput
 *
 * کامپوننت ورودی تاریخ با فرمت شمسی
 * این کامپوننت تاریخ را با فرمت YYYY/MM/DD دریافت می‌کند
 * و از ماسک JavaScript برای اعتبارسنجی استفاده می‌کند
 *
 * @see DateInputTest
 */
class DateInput extends TextInput
{
    /**
     * تنظیمات اولیه کامپوننت
     * شامل ماسک ورودی، placeholder و ویژگی‌های اضافی
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->mask(RawJs::make(<<<'JS'
        $input.startsWith('34') || $input.startsWith('37') ? '9999/99/99' : '9999/99/99'
    JS
            ))
            ->placeholder('مثال: 1394/01/01')
            ->extraInputAttributes([
                'inputmode' => 'numeric',
                'dir' => 'ltr',
            ]);
    }
}
