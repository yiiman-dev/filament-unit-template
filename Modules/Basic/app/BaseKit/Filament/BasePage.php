<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:42 PM
 */

namespace Modules\Basic\BaseKit\Filament;


use CodeWithDennis\SimpleAlert\Components\Infolists\SimpleAlert;
use Filament\Notifications\Notification;
use Modules\Basic\BaseKit\Filament\Concerns\CheckPageStandards;
use Filament\Pages\Page;
use RealRashid\SweetAlert\Facades\Alert;

class BasePage extends Page
{
    use CheckPageStandards;
    use HasNotification;
    public function __construct()
    {
        $this->checkDevelopentStandards();
    }


}
