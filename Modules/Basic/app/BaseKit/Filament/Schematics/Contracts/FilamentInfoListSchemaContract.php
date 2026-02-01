<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/4/25, 2:56 PM
 */

namespace Modules\Basic\BaseKit\Filament\Schematics\Contracts;

use Filament\Forms\Form;
use Filament\Tables\Table;

interface FilamentInfoListSchemaContract
{
    function infoListSchema(): array;


    public function attributeHints(): array;

    public function attributeHelperTexts(): array;
}
