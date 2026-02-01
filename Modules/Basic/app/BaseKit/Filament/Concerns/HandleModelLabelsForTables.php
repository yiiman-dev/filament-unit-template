<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:44 PM
 */

namespace Modules\Basic\BaseKit\Filament\Concerns;

use Filament\Tables\Columns\Column;
use Modules\Basic\Concerns\HasAttributeLabels;

trait HandleModelLabelsForTables
{
    protected function applyModelLabels(string $name): static
    {
        $this->label($this->getLabelClosure($name));

        return $this;
    }

    protected function getLabelClosure(string $name): \Closure
    {
        return function () use ($name) {
            // Get the model class from the Livewire component or resource
            $livewire = $this->getLivewire();

            $modelClass = null;
            if (method_exists($livewire, 'getModel')) {
                $modelClass = $livewire->getModel();
            } elseif (property_exists($livewire, 'record') && $livewire->record) {
                $modelClass = get_class($livewire->record);
            } elseif (property_exists($livewire, 'resource')) {
                $resourceClass = $livewire->resource;
                if (property_exists($resourceClass, 'model')) {
                    $modelClass = $resourceClass::$model;
                }
            }

            if (!$modelClass || !in_array(HasAttributeLabels::class, class_uses_recursive($modelClass))) {
                return $this->evaluate('label');
            }

            $modelInstance = app($modelClass);
            return $modelInstance->getAttributeLabel($name);
        };
    }


}
