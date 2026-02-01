<?php

namespace Enums;

enum ActivationEnum: int
{
    case ACTIVE = 1;
    case DEACTIVATE = 0;

    public static function getLabels(): array
    {
        return [
            self::ACTIVE->value => 'فعال',
            self::DEACTIVATE->value => 'غیرفعال',
        ];
    }
}
