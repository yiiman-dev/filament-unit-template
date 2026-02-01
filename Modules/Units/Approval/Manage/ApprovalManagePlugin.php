<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/3/25, 10:09 AM
 */

namespace Units\Approval\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Approval\Manage\Filament\Resources\ApprovalFlowResource;
use Units\DocumentConditionTemplate\Manage\Filament\DocumentConditionsTemplateResource;

class ApprovalManagePlugin implements Plugin
{
    public static function make()
    {
        return new static;
    }

    public function getId(): string
    {
        return 'filament-approval-manage';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->plugins([\EightyNine\Approvals\ApprovalPlugin::make()])
        ->resources([
            ApprovalFlowResource::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
