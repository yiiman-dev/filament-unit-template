<?php

namespace Modules\Basic\BaseKit\Filament\Components\Table;

use Filament\Tables\Columns\TextColumn as BaseTextColumn;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabelsForTables;

class TextColumn extends BaseTextColumn
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
