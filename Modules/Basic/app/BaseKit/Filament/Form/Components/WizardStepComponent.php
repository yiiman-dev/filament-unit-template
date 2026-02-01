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

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use phpDocumentor\Reflection\Types\Self_;

/**
 * WizardStepComponent
 *
 * کامپوننت نمایش مراحل فرم به صورت ویژوال
 * این کامپوننت مراحل فرم را به صورت یک نوار پیشرفت نمایش می‌دهد
 * که شامل شماره مرحله، عنوان و آیکون اختیاری است
 *
 * @see WizardStepComponentTest
 * @see resources/views/components/wizard-step.blade.php
 */
class WizardStepComponent extends Section
{
    protected string $view = 'components.wizard-step';

    /**
     * لیست مراحل فرم
     *
     * @var array
     */
    public array $steps = [];

    /**
     * شماره مرحله فعلی
     *
     * @var int
     */
    public int $currentStep = 1;

    /**
     * آیکون اختیاری برای مراحل
     *
     * @var string|null
     */
    public ?string $stepIcon = null;


    public function stepIcon($icon):self
    {
        $this->stepIcon=$icon;
        return $this;
    }

    public function currentStep($step):self
    {
        $this->currentStep=$step;
        return $this;
    }


    public function steps(array $steps):self
    {
        $this->steps=$steps;
        return $this;
    }
    /**
     * دریافت داده‌های مورد نیاز برای نمایش
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'steps' => $this->steps,
            'currentStep' => $this->currentStep,
            'stepIcon' => $this->stepIcon,
        ];
    }
}
