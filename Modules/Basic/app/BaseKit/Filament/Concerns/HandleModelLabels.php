<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:44 PM
 */

namespace Modules\Basic\BaseKit\Filament\Concerns;

use Filament\Forms\Components\Component;
use Modules\Basic\Concerns\HasAttributeLabels;

trait HandleModelLabels
{
    protected function applyModelLabels(string $name): static
    {
        $this->label($this->getLabelClosure($name));
        $this->helperText($this->getHelperTextClosure($name));

        return $this;
    }

    protected function getLabelClosure(string $name): \Closure
    {
        return function (Component $component) use ($name) {
            $model = $component->getRecord() ?? $component->getModel();

            if (!$model || !in_array(HasAttributeLabels::class, class_uses_recursive($model))) {
                return $this->evaluate('label');
            }

            $modelInstance = is_string($model) ? app($model) : $model;
            return $modelInstance->getAttributeLabel($name);
        };
    }

    protected function getHelperTextClosure(string $name): \Closure
    {
        return function (Component $component) use ($name) {
            $model = $component->getRecord() ?? $component->getModel();

            if (!$model || !in_array(HasAttributeLabels::class, class_uses_recursive($model))) {
                return $this->evaluate('helperText');
            }

            $modelInstance = is_string($model) ? app($model) : $model;
            return $modelInstance->getAttributeHint($name);
        };
    }
}
