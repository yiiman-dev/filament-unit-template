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
 * Widget for displaying financial supply executive process tracking table
 */
class FinancialProcessTrackingWidget extends Widget
{
    protected static string $view = 'units.dashboard.manage.widgets.financial-process-tracking';

    protected int|string|array $columnSpan = 'full'; // کل عرض صفحه

    /**
     * Get process stages
     *
     * @return array
     */
    public function getStages(): array
    {
        return [
            'درخواست اولیه',
            'اعلام شرایط',
            'موافقت بنگاه',
            'تنظیم مفاد تفاهم نامه',
            'تایید مفاد تفاهم نامه',
            'امضا و ابلاغ',
            'درخواست مصوبه',
            'قبول بنگاه',
            'صورتحساب',
            'صدور معرفی نامه',
            'مصوبه اعتباری',
            'اجرای مصوبه',
            'تسویه کارمزد',
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
                'reference_number' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    'success', 'success', 'success', 'success', 'success', 'success', 'success',
                    'failed', // قبول بنگاه
                    null, null, null, null, null,
                ],
            ],
            [
                'reference_number' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    'success', 'success', 'success', 'success', 'success', 'success', 'success',
                    'success', 'success', 'success',
                    'warning', // مصوبه اعتباری
                    null, null,
                ],
            ],
            [
                'reference_number' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    'success', 'success', 'success', 'success', 'success', 'success', 'success',
                    'success', 'success', 'success', 'success', 'success', 'success',
                ],
            ],
            [
                'reference_number' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    'success', 'success', 'success', 'success', 'success', 'success', 'success',
                    'success', 'success', 'success', 'success', 'success', 'success',
                ],
            ],
            [
                'reference_number' => '۱۲۳۴',
                'date' => '۱۴۰۳/۰۸/۳۰',
                'value' => '۶۰۰ میلیارد ریال',
                'stages' => [
                    'success', 'success', 'success', 'success', 'success', 'success', 'success',
                    'success', 'success', 'success', 'success', 'success',
                    'failed', // تسویه کارمزد
                ],
            ],
        ];
    }
}

