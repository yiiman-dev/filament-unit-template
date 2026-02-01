<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/5/25, 10:32 PM
 */

namespace Units\Auth\Admin\Filament\Pages;


use Filament\Pages\Concerns\HasMaxWidth;
use Units\Panels\Admin\Filament\Pages\BaseLayoutComponent;

class AuthLayoutComponent extends BaseLayoutComponent
{

    use HasMaxWidth;
    protected static string $layout='admin_auth::filament.layout.auth';
    protected function getLayoutData(): array
    {
        return [
          'maxWidth'=>$this->getMaxWidth()
        ];
    }


}
