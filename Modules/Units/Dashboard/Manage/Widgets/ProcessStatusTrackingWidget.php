<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          1/15/25, 12:00 PM
 */

namespace Units\Dashboard\Manage\Widgets;

use Filament\Widgets\Widget;

/**
 * Widget for displaying process status tracking table
 */
class ProcessStatusTrackingWidget extends Widget
{
    protected static string $view = 'units.dashboard.manage.widgets.process-status-tracking';

    protected int|string|array $columnSpan = 'full'; // کل عرض صفحه

    /**
     * Get process stages
     *
     * @return array
     */
    public function getStages(): array
    {
        return [
            ['number' => 0, 'name' => '#'],
            ['number' => 1, 'name' => 'درخواست اولیه'],
            ['number' => 2, 'name' => 'انعقاد تفاهم نامه'],
            ['number' => 3, 'name' => 'درخواست مصوبه'],
            ['number' => 4, 'name' => 'صدور مصوبه'],
            ['number' => 5, 'name' => 'اجرای مصوبه'],
        ];
    }

    /**
     * Get sample process data
     *
     * @return array
     */
    public function getProcessData(): array
    {
        return [
            [
                'request_id' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    ['status' => 'waiting_broker', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    null,
                    null,
                    null,
                    null,
                ],
            ],
            [
                'request_id' => '۱۵۴۳',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'waiting_me', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    null,
                    null,
                    null,
                ],
            ],
            [
                'request_id' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'stopped', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    null,
                    null,
                ],
            ],
            [
                'request_id' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'waiting_broker', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    null,
                ],
            ],
            [
                'request_id' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                    ['status' => 'done', 'datetime' => '۱۴۰۴/۰۸/۳۰ - ۱۴:۵۴'],
                ],
            ],
        ];
    }

    /**
     * Get status label and color
     *
     * @param string|null $status
     * @return array
     */
    public function getStatusInfo(?string $status): array
    {
        return match($status) {
            'done' => ['label' => 'انجام شده', 'color' => 'green'],
            'waiting_broker' => ['label' => 'در انتظار کارگزار', 'color' => 'yellow'],
            'waiting_me' => ['label' => 'در انتظار من', 'color' => 'yellow'],
            'stopped' => ['label' => 'متوقف شده', 'color' => 'red'],
            default => ['label' => '', 'color' => 'gray'],
        };
    }
}

