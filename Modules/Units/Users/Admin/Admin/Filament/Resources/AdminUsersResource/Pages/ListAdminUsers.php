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
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource;
use Units\Users\Admin\Admin\Services\UserService;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
            ->label('همه کاربران')
            ,
            'active' => Tab::make()
                ->label('کاربران فعال')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', UserService::STATUS_ACTIVE)),
            'de_active' => Tab::make()
                ->label('کاربران غیرفعال')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', UserService::STATUS_DE_ACTIVE)),

        ];
    }
}
