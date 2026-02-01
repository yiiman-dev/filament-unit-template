<?php

namespace Units\Chat\Common\Enums;

enum ChatPersonaEnum: string
{
    case CORPORATE_MANAGE = 'CM';
    case MANAGE_MANAGE='MM';
    case MANAGE_ADMIN='MA';
    case MYUSER_MANAGE='MUM';
    case CORPORATE_CORPORATE='CC';

    case COMMENT='COM';
}
