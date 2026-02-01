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
use Illuminate\Contracts\Support\Htmlable;
use Modules\Basic\Helpers\Helper;

/**
 * MobileInput
 *
 * کامپوننت ورودی شماره همراه با اعتبارسنجی
 * این کامپوننت شماره همراه را با فرمت بین‌المللی دریافت می‌کند
 * و از regex pattern برای اعتبارسنجی استفاده می‌کند
 *
 * @see MobileInputTest
 */
class MobileInput extends TextInput
{
    /**
     * الگوی regex برای اعتبارسنجی شماره همراه
     *
     * @var string|Closure|null
     */
    protected string | Closure | null $regexPattern = Helper::MOBILE_REGEX;

    /**
     * فعال بودن اعتبارسنجی زنده
     *
     * @var bool|Closure|null
     */
    protected bool | Closure | null $isLive = true;


    protected string | Htmlable | Closure | null $label='شماره همراه';

    /**
     * دریافت ویژگی‌های اضافی Alpine.js
     * شامل tabindex و استایل‌های جهت‌دار
     *
     * @return array
     */
    public function getExtraAlpineAttributes(): array
    {
        return ['tabindex' => 1, 'style' => 'text-align:left;direction:ltr'];
    }

    /**
     * پیام‌های خطای اعتبارسنجی
     *
     * @var array
     */
    protected array $validationMessages = [
        'unique' => 'شماره قبلا ثبت شده است',
        'regex' => 'فرمت شماره همراه اشتباه است٬ دقت کنید زبان کیبرد انگلیسی باشد و شماره همراه مانند فرمت روبرو باشد: 989123456789+',
    ];
}
