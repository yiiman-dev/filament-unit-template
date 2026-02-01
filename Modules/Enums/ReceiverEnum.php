<?php

namespace Enums;

enum ReceiverEnum: int
{
    case COMPANY = 1;
    case FINANCER = 2;

    public static function getLabels(): array
    {
        return [
            self::COMPANY->value => 'شرکت SCF',
            self::FINANCER->value => 'فاینانسر عامل',
        ];
    }
}
