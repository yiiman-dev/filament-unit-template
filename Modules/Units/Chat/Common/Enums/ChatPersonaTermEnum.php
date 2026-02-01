<?php

namespace Units\Chat\Common\Enums;

enum ChatPersonaTermEnum: string
{
    case CORPORATE = 'C';
    case ADMIN_PANEL = 'AP';
    case MANAGE_PANEL = 'MP';
    case MANAGE_USER = 'MU';
    case ADMIN_USER = 'AU';
    case MY_USER = 'MYU';
}
