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

namespace Units\Users\Manage\Manage\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Units\Users\Manage\Manage\Filament\Resources\UserResource;

/**
 * صفحه لیست کاربران
 * این صفحه از مدل User که از APIModel ارث‌بری می‌کند استفاده می‌کند
 */
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
