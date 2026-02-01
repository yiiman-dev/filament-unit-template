<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\DateTimePicker as BaseDateTimePicker;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class DateTimePicker extends BaseDateTimePicker
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
