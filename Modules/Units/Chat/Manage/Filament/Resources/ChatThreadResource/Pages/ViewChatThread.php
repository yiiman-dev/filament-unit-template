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
use Filament\Resources\Pages\ViewRecord;
use Modules\Units\Chat\Common\Filament\Widgets\ChatFormWidget;
use Modules\Units\Chat\Common\Filament\Widgets\ChatInfolistWidget;
use Modules\Units\Chat\Manage\Filament\Resources\ChatThreadResource;

/**
 * صفحه نمایش ترد چت
 * View chat thread page
 *
 * صفحه برای نمایش جزئیات یک ترد چت
 * Page for displaying chat thread details
 */
class ViewChatThread extends ViewRecord
{
    protected static string $resource = ChatThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ChatInfolistWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ChatFormWidget::class,
        ];
    }
}
