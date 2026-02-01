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
use Faker\Factory;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\Rules\Password;

/**
 * PasswordInput
 *
 * کامپوننت ورودی رمز عبور با اعتبارسنجی پیشرفته
 * این کامپوننت رمز عبور را با قوانین امنیتی دریافت می‌کند
 * و قابلیت تولید رمز عبور تصادفی را دارد
 *
 * @see PasswordInputTest
 */
class PasswordInput extends \Rawilk\FilamentPasswordInput\Password
{
    /**
     * حداقل طول رمز عبور
     * 
     * @var int
     */
    protected $min = 8;

    /**
     * دریافت قوانین اعتبارسنجی رمز عبور
     * شامل حداقل طول، حروف و اعداد
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            (new Password($this->min))->letters()->numbers()
        ];
    }

    /**
     * تنظیم رمز عبور پیش‌فرض تصادفی
     * از Faker برای تولید رمز عبور استفاده می‌کند
     *
     * @param mixed $state
     * @return static
     */
    public function default(mixed $state): static
    {
        $this->hasDefaultState = true;
        $this->defaultState = Factory::create('en')->password();
        return $this;
    }

    protected bool|Closure|null $isLive = true;

    public function getLabel(): string|Htmlable|null
    {
        return !empty($this->label) ?$this->label:'رمز عبور';
    }


    public function getExtraAlpineAttributes(): array
    {
        return ['tabindex' => 1, 'style' => 'text-align:left;color:red'];
    }

    public function getValidationMessages(): array
    {
        return [
            'min' =>
                [
                    'string' => 'حداقل تعداد کاراکتر باید ' . $this->min . ' عدد باشد'
                ],
            'password' =>
                [
                    'letters' => 'رمز عبور باید شامل حروف انگلیسی باشد',
                    'numbers' => 'رمز عبور باید شامل عدد باشد'
                ]
        ];
    }
}
