<?php

namespace Modules\Basic\BaseKit\Filament\Components\Table;

use Filament\Tables\Columns\CheckboxColumn as BaseCheckboxColumn;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabelsForTables;

class CheckboxColumn extends BaseCheckboxColumn
{
    use HandleModelLabelsForTables;
    public static function make(?string $name = null): static
    {
        $component = parent::make($name);

        if ($name) {
            $component->applyModelLabels($name);
        }

        return $component;
    }
}
