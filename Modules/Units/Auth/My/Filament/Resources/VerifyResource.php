<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:58 PM
 */

namespace Units\Auth\My\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class VerifyResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form;
    }



    private static function verify_code_component()
    {
        return TextInput::make('verification_code')
            ->label('کد اعتبارسنجی')
            ->required()
            ->mask('9-9-9-9-9-9');
    }
}
