<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\Textarea as BaseTextarea;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class Textarea extends BaseTextarea
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
