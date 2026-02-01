<?php

namespace Enums;

enum PartyEnum: int
{
    case SELLER = 1;
    case BUYER = 2;

    public static function getLabels(): array
    {
        return [
            self::SELLER->value => 'فروشنده',
            self::BUYER->value => 'خریدار',
        ];
    }
}
