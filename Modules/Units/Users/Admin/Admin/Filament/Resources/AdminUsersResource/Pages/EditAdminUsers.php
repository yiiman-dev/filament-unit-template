<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:53 PM
 */

namespace Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource;

class EditAdminUsers extends EditRecord
{
    protected static string $resource = AdminUsersResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {

    }
}
