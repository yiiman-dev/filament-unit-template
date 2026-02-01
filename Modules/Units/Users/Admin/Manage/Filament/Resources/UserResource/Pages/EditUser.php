<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 6:11 PM
 */

namespace Units\Users\Admin\Manage\Filament\Resources\UserResource\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Modules\Basic\BaseKit\Filament\Form\Components\MobileInput;
use Modules\Basic\Rules\RemoteUnique;
use Units\Users\Admin\Manage\Filament\Resources\UserResource;
use Units\Users\Admin\Manage\Models\User;
use Units\Users\Admin\Manage\Services\UserService;

/**
 * صفحه ویرایش کاربر
 * این صفحه از مدل User که از APIModel ارث‌بری می‌کند استفاده می‌کند
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;


    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('username')
                    ->extraAlpineAttributes(['tabindex' => 1])
                    ->rule((
                        new RemoteUnique(app(User::class), 'username', ' نام کاربری')
                    ))
                    ->label('نام کاربری')
                    ->validationMessages([
                        'unique' => 'کاربر تکراری است'
                    ])
                    ->extraAlpineAttributes([
                        'style' => 'text-align:left;direction:ltr'
                    ]),
                MobileInput::make('phone_number')
                    ->extraAlpineAttributes(['tabindex' => 2])
                    ->rule((
                    new RemoteUnique(app(User::class), 'phone_number', 'شماره همراه')
                    ))
                    ->required(),
                Select::make('status')
                    ->options(
                        [
                            UserService::STATUS_ACTIVE => 'کاربر فعال',
                            UserService::STATUS_DE_ACTIVE => 'غیر فعال (عدم امکان ورود به پنل)'
                        ]
                    )
                    ->disabled(function ($record) {
                        if (!empty($record)) {
                            return true;
                        }
                    }),
                TextInput::make('created_by')
                    ->label('ایجاد شده توسط')
                    ->extraAlpineAttributes([
                        'style' => 'text-align:left;direction:ltr'
                    ])
                    ->disabled()
                    ->helperText(function ($record) {
                        if (isset($record['created_by']) && $record['created_by'] == $logged_in_user = Filament::auth()->user()->phone_number) {
                            return 'شما این کاربر را ایجاد کردید';
                        }
                    })
            ]);
    }
}
