<?php

namespace Enums;

enum ResourceTypeEnum: string
{
    case MONEY_MARKET = 'money_market';
    case CAPITAL_MARKET = 'capital_market';
    case CORPORATE = 'corporate';

    public static function getLabels(): array
    {
        return [
            self::MONEY_MARKET->value => 'بازار پول',
            self::CAPITAL_MARKET->value => 'بازار سرمایه',
            self::CORPORATE->value => 'شرکتی',
        ];
    }

    public static function getProcedures(): array
    {
        return [
            self::MONEY_MARKET->value => [
                'facilities' => 'تسهیلات',
                'commitments' => 'تعهدات',
                'novin' => 'نوین',
            ],
            self::CAPITAL_MARKET->value => [
                'sukuk' => 'صکوک',
                'crowdfunding' => 'تأمین مالی جمعی',
            ],
            self::CORPORATE->value => [
                'leasing' => 'لیزینگ',
                'factoring' => 'فاکتورینگ',
                'reverse_factoring' => 'فاکتورینگ معکوس',
            ],
        ];
    }

    public static function getTools(): array
    {
        return [
            'facilities' => [
                'murabaha' => 'مرابحه',
                'modaraba' => 'مضاربه',
                'kharid_deyn' => 'خرید دین',
                'forosh_aghsati' => 'فروش اقساطی',
                'estesna' => 'استصناع',
            ],
            'commitments' => [
                'gam' => 'اوراق گام',
                'e_barat' => 'برات الکترونیک',
                'zemanatnameh' => 'ضمانتنامه',
                'etebar_asnad_dakheli' => 'اعتبار اسنادی داخلی',
            ],
            'novin' => [
                'factoring' => 'فاکتورینگ',
                'reverse_factoring' => 'فاکتورینگ معکوس',
            ],
        ];
    }

    public static function getToolsByProcedure(?string $procedure): array
    {
        return self::getTools()[$procedure] ?? [];
    }


    public static function getProceduresByType(?string $type): array
    {
        return self::getProcedures()[$type] ?? [];
    }
}
