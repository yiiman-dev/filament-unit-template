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

interface FilamentFormSchemaContract
{
    function commonFormSchema(): array;

    function editFormSchema(): array|null;

    function createFormSchema(): array|null;

    public function attributeHints(): array;

    public function attributePlaceholders(): array;

    public function attributeDefaults(): array;

    public function attributeHelperTexts(): array;
}
