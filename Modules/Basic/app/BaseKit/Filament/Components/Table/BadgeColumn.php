<?php

namespace Modules\Basic\BaseKit\Filament\Components\Table;

use Filament\Tables\Columns\CheckboxColumn as BaseCheckboxColumn;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabelsForTables;

class BadgeColumn extends \Filament\Tables\Columns\BadgeColumn
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

    public function trueIcon($icon): self
    {
        if ($this->getState()) {
            $this->icon($icon);
        }
        return $this;
    }

    public function trueColor($color): self
    {
        if ($this->getState()) {
            $this->color($color);
        }
        return $this;
    }

    public function falseColor($color): self
    {
        if (empty($this->getState())) {
            $this->color($color);
        }
        return $this;
    }

    public function falseIcon($icon): self
    {
        if (!$this->getState()) {
            $this->icon($icon);
        }
        return $this;
    }


}
