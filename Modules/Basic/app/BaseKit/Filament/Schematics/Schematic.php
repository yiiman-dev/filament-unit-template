<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/4/25, 2:58 PM
 */

namespace Modules\Basic\BaseKit\Filament\Schematics;

use Filament\Forms\Components\Component;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Illuminate\Support\Arr;
use Modules\Basic\BaseKit\Filament\Schematics\Contracts\SchematicContract;

abstract class Schematic implements SchematicContract
{
    public array $attributes = [];
    private array $disabled_attributes = [];

    private array $labels = [];

    public array $visible_attributes = [];


    protected Schematic $remoteClass;

    protected function initSchema(): void
    {
        $this->labels = $this->attributeLabels();
        if (!empty($this->invisibleAttributes())) {
            $this->visible_attributes = Arr::except(
                $this->visible_attributes,
                array_keys($this->invisibleAttributes())
            );
        }
        $this->disabled_attributes = $this->disableAttributes();
    }


    /**
     * When using this schematic in another one, you should provide that class to ability override properties like label from that schema
     * This will provide deeply using form in another and ability to customize them
     * @param Schematic $schematic
     * @return self
     */
    public function remoteSchema(Schematic $schematic): self
    {
        $this->remoteClass = $schematic;
        return $this;
    }

    public function getAttributeLabel($attribute)
    {
        return Arr::get(
            $this->labels,
            $attribute,
            str($attribute)
                ->trim()
                ->pascal()
                ->toString()
        );
    }

    public function getActions(): array
    {
        return [];
    }

    public function setAttributeLabel(string $attribute, string $label): self
    {
        $this->labels[$attribute] = $label;
//        if (isset($this->attributes[$attribute])){
//            $this->attributes[$attribute]->label($label);
//        }
        return $this;
    }

    public function isVisible(string $attribute): bool
    {
        return !$this->isInvisible($attribute);
    }

    public function isInvisible(string $attribute): bool
    {
        return !Arr::exists($this->visible_attributes, $attribute);
    }

    public function isDisabled(string $attribute): bool
    {
        $exists = Arr::exists($this->disabled_attributes, $attribute);
        if (!$exists) {
            $exists = Arr::exists($this->disabled_attributes, $attribute . '_save');
        }
        return $exists;
    }

    public function canSaveOnDisable(string $attribute)
    {
        if (!empty($this->disabled_attributes[$attribute])) {
            $value = $this->disabled_attributes[$attribute];
        }
        if ($value == 'disabled_save') {
            return true;
        }
        return false;
    }

    public function isNotDisabled(string $attribute): bool
    {
        return !$this->isDisabled($attribute);
    }

    public function visibleAttribute(string $attribute, $label = null): self
    {
        $this->visible_attributes[$attribute] = 0;

        if (!empty($this->remoteClass)) {
            $remote_label = $this->remoteClass->getAttributeLabel($attribute);
            if ($remote_label) {
                $this->setAttributeLabel($attribute, $remote_label);
            }
        }
        if (!empty($label)) {
            $this->setAttributeLabel($attribute, $label);
        }
        return $this;
    }

    /**
     * inject input between form inputs
     * @param $attribute_name
     * @param Component $input_component
     * @return self
     */
    public function injectInput($attribute_name, Component $input_component): self
    {
        $this->visible_attributes[$attribute_name] = $input_component;
        $this->attributes[$attribute_name] = $input_component;
        if (!empty($this->remoteClass)) {
            $remote_label = $this->remoteClass->getAttributeLabel($attribute_name);
            if ($remote_label) {
                $this->setAttributeLabel($attribute_name, $remote_label);
            }
        }
        return $this;
    }

    public function invisibleAttribute(string $attribute, bool $should_disable = true): self
    {
        unset($this->visible_attributes[$attribute]);
        if ($should_disable) {
            $this->disableAttribute($attribute);
        }
        return $this;
    }

    public function disableAttribute(string $attribute, $save = false): self
    {
        $this->disabled_attributes[$attribute] = 'disabled' . ($save ? '_save' : '');
        return $this;
    }

    public static function merge(array $old, array $new): array
    {
        return array_merge_recursive($old, $new);
    }


}
