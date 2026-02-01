<?php

namespace Enums;

enum ModelTypeEnum: int
{
    case BANKING = 1;
    case FACILITY = 2;
    case OTHER = 3;

    public static function getLabels(): array
    {
        return [
            self::BANKING->value => 'بانکی',
            self::FACILITY->value => 'تسهیلاتی',
            self::OTHER->value => 'غیر',
        ];
    }
}
