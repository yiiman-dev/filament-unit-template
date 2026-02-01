<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\Checkbox as BaseCheckbox;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class Checkbox extends BaseCheckbox
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
