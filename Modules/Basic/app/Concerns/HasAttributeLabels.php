<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/13/25, 1:38 PM
 */

namespace Modules\Basic\Concerns;


use Filament\Forms\Components\TextInput;


trait HasAttributeLabels
{
    /**
     * Define labels for model attributes
     * Override this method in your model to define custom labels
     *
     * @return array
     */
    protected function attributeLabels(): array
    {
        return [];
    }

    /**
     * Define hints/descriptions for model attributes
     * Override this method in your model to define custom hints
     *
     * @return array
     */
    protected function attributeHints(): array
    {
        return [];
    }

    /**
     * Get label for a specific attribute
     * Returns the custom label if defined, otherwise returns the attribute name
     *
     * @param string $attribute
     * @return string
     */
    public function getAttributeLabel(string $attribute): string
    {
        $labels = $this->attributeLabels();

        return $labels[$attribute] ?? $attribute;
    }

    /**
     * Get hint/description for a specific attribute
     * Returns the custom hint if defined, otherwise returns empty string
     *
     * @param string $attribute
     * @return string
     */
    public function getAttributeHint(string $attribute): string
    {
        $hints = $this->attributeHints();

        return $hints[$attribute] ?? '';
    }

}
