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

use Filament\Resources\Pages\ViewRecord;
use Units\Users\Admin\Manage\Filament\Resources\UserResource;

/**
 * صفحه نمایش کاربر
 * این صفحه از مدل User که از APIModel ارث‌بری می‌کند استفاده می‌کند
 */
class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
}
