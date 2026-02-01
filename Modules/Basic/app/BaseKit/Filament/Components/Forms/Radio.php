<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\Radio as BaseRadio;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class Radio extends BaseRadio
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
