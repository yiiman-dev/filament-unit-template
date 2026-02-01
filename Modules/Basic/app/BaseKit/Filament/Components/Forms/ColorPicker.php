<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\ColorPicker as BaseColorPicker;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class ColorPicker extends BaseColorPicker
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
