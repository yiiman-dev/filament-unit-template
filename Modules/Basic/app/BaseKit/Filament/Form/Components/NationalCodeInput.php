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


/**
 * User NationalCode
 *
ورودی کد ملی کاربر برای فرم های فیلامنت
 *
 * @see PasswordInputTest
 */
class NationalCodeInput extends TextInput
{
    protected int | Closure | null $length=13;
    protected string | RawJs | Closure | null $mask='999-999-999-9';



    protected bool|Closure|null $isLive = true;

    public function getLabel(): string|Htmlable|null
    {
        return !empty($this->label) ?$this->label:'کد ملی کاربر';
    }


    public function getExtraAlpineAttributes(): array
    {
        return ['tabindex' => 1, 'style' => 'text-align:left;color:red'];
    }

    public function getValidationMessages(): array
    {
        return [
            'size'=> 'کد ملی باید ۱۰ رقمی باشد',
        ];
    }
}
