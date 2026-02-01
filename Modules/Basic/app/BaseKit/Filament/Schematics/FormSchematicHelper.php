<?php

namespace Modules\Basic\BaseKit\Filament\Schematics;

use Modules\Basic\BaseKit\Filament\Schematics\Concerns\InteractWithForm;

class FormSchematicHelper extends Schematic
{
    use InteractWithForm;
    public static function make():self{
        return new static();
    }

    public function attributeLabels(): array
    {
        return [];
    }

    public function invisibleAttributes(): array
    {
        return [];
    }

    public function disableAttributes(): array
    {
        return [];
    }

    public function getAttributeHint()
    {
        return null;
    }
    public function getAttributeHelperText()
    {
        return null;
    }

    public function getAttributePlaceholder()
    {
        return null;
    }

    public function getAttributeDefault()
    {
        return null;
    }
}
