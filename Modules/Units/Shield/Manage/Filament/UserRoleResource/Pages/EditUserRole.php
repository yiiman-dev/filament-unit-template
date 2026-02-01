<?php

namespace Units\Shield\Manage\Filament\UserRoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Units\Shield\Manage\Filament\UserRoleResource;

class EditUserRole extends EditRecord
{
    protected static string $resource = UserRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
