<?php

namespace Modules\Basic\Enums;

enum ContractTemplateTypeEnum: int
{
    case MEMORANDUM_OF_UNDERSTANDING = 1;

    case AGREEMENT = 2;

    case CONTRACT = 3;

    public static function getLabels(): array
    {
        return [
            self::MEMORANDUM_OF_UNDERSTANDING->value => 'تفاهم نامه',
            self::AGREEMENT->value => 'موافقت نامه',
            self::CONTRACT->value => 'قرارداد',
        ];
    }
}