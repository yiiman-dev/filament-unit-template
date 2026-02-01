<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/4/25, 4:28 PM
 */

namespace Modules\Basic\BaseKit\Filament\Schematics\Concerns;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;

use Modules\Basic\BaseKit\Filament\Components\Table\TextColumn;
use Modules\Basic\BaseKit\Filament\Form\Components\MoneyColumn;
use Modules\Basic\Helpers\Helper;
use Mokhosh\FilamentRating\Columns\RatingColumn;
use mysql_xdevapi\Table;

trait InteractWithTable
{
    public function textColumn($attribute): TextColumn
    {
        return TextColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function moneyColumn($attribute): MoneyColumn
    {
        return MoneyColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function badgeColumn($attribute): BadgeColumn
    {
        return \Modules\Basic\BaseKit\Filament\Components\Table\BadgeColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function booleanColumn($attribute): BooleanColumn
    {
        return BooleanColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function colorColumn($attribute): ColorColumn
    {
        return ColorColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function iconColumn($attribute): IconColumn
    {
        return IconColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function imageColumn($attribute): ImageColumn
    {
        return ImageColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function selectColumn($attribute): SelectColumn
    {
        return SelectColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function tagsColumn($attribute): TagsColumn
    {
        return TagsColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function toggleColumns($attribute): ToggleColumn
    {
        return ToggleColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function viewColumn($attribute): ViewColumn
    {
        return ViewColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute));
    }

    public function mobileColumn($attribute):TextColumn
    {
        return TextColumn::make($attribute)
            ->disabled($this->isDisabled($attribute))
            ->visible($this->isVisible($attribute))
            ->label($this->getAttributeLabel($attribute))
            ->formatStateUsing(fn($state)=>Helper::denormalize_phone_number($state))
            ->copyable()
            ->searchable()
            ->alignCenter()
            ->extraAttributes(['style' => 'direction:ltr;text-align:center']);
    }

    public function dateColumn($attribute)
    {
        return $this->textColumn($attribute)
            ->jalaliDate();
    }

    public function dateTimeColumn($attribute)
    {
        return $this->textColumn($attribute)
            ->jalaliDateTime();
    }


    public function ratingColumn($attribute):RatingColumn
    {
        return RatingColumn::make($attribute);
    }

}
