<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:31 AM
 */

namespace Modules\Units\Chat\Manage\Filament\Resources\ChatThreadResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Units\Chat\Manage\Filament\Resources\ChatThreadResource;

/**
 * صفحه لیست تردهای چت
 * List chat threads page
 *
 * صفحه برای نمایش لیست تردهای چت
 * Page for displaying chat thread list
 */
class ListChatThreads extends ListRecords
{
    protected static string $resource = ChatThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
