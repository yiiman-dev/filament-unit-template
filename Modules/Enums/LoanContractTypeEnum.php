<?php

namespace Enums;

enum LoanContractTypeEnum: int
{
    case MURABAHA = 1;
    case DEBT_PURCHASE = 2;
    case MUDARABA = 3;

    public static function getLabels(): array
    {
        return [
            self::MURABAHA->value => 'مرابحه',
            self::DEBT_PURCHASE->value => 'خرید دین',
            self::MUDARABA->value => 'مضاربه',
        ];
    }
}
