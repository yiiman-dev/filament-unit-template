<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/4/25, 3:06 PM
 */

namespace Modules\Basic\BaseKit\Filament\Schematics\Contracts;

interface SchematicContract
{
    public function attributeLabels():array;
    public function invisibleAttributes():array;
    public function disableAttributes():array;
}
