<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\FileUpload as BaseFileUpload;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class FileUpload extends BaseFileUpload
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
