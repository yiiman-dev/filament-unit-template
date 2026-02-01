<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:46 AM
 */

namespace Units\Auth\My\Filament\Pages;


use Filament\Pages\BasePage;
use Filament\Pages\Concerns\HasMaxWidth;
use Units\Panels\My\Filament\Pages\BaseLayoutComponent;

class AuthLayoutComponent extends BasePage
{

    use HasMaxWidth;
    protected static string $layout='my_auth::filament.layout.auth';
    protected function getLayoutData(): array
    {
        return [
          'maxWidth'=>$this->getMaxWidth()
        ];
    }


}
