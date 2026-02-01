<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\TimePicker as BaseTimePicker;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class TimePicker extends BaseTimePicker
{
    use HandleModelLabels;
    public static function make(?string $name = null): static
    {
        $component = parent::make($name);

        if ($name) {
            $component->applyModelLabels($name);
        }

        return $component;
    }
}
