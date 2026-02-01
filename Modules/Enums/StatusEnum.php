<?php

namespace Enums;

enum StatusEnum: string
{
    case WAITING = 'waiting';
    case IN_PROGRESS = 'in_progress';
    case ACCEPT = 'accept';
    case REJECT = 'reject';
    case ARCHIVED = 'archived';

    public static function getLabels(): array
    {
        return [
            self::WAITING->value => 'درانتظار پذیرش',
            self::IN_PROGRESS->value => 'درحال بررسی',
            self::ACCEPT->value => 'تایید درخواست',
            self::REJECT->value => 'رد درخواست',
            self::ARCHIVED->value => 'بایگانی شده',
        ];
    }

    public static function getColors(): array
    {
        return [
            'primary' => self::WAITING->value,
            'warning' => self::IN_PROGRESS->value,
            'success' => self::ACCEPT->value,
            'danger' => self::REJECT->value,
            'gray' => self::ARCHIVED->value,
        ];
    }

    public static function getIcons(): array
    {
        return [
            'heroicon-o-clock' => self::WAITING->value,
            'heroicon-o-arrow-path' => self::IN_PROGRESS->value,
            'heroicon-o-check-circle' => self::ACCEPT->value,
            'heroicon-o-x-circle' => self::REJECT->value,
        ];
    }


}
