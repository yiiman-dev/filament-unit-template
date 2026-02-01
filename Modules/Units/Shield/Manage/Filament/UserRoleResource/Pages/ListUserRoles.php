<?php

namespace Units\Shield\Manage\Filament\UserRoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Units\Shield\Manage\Filament\UserRoleResource;

class ListUserRoles extends ListRecords
{
    protected static string $resource = UserRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
