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

use Filament\Widgets\ChartWidget;

/**
 * Widget for displaying average waiting time in each process as a bar chart
 */
class AverageWaitingTimeBarChartWidget extends ChartWidget
{
    protected static ?string $heading = 'میانگین زمان انتظار در هر یک از فرآیندها';

    protected int | string | array $columnSpan = [
        'md' => 7,
        'xl' => 7,
    ]; // 6 قسمت از 12 قسمت (1/2)

    protected function getData(): array
    {
        // داده‌های استاتیک - می‌توانید بعداً با دیتابیس جایگزین کنید
        return [
            'datasets' => [
                [
                    'label' => 'میانگین زمان انتظار (ساعت)',
                    'data' => [45, 35, 59, 42, 38, 28, 55, 48, 52, 60, 40, 35, 30],
                    'backgroundColor' => [
                        '#f472b6', // صورتی
                        '#fb923c', // نارنجی
                        '#fbbf24', // زرد
                        '#60a5fa', // آبی روشن
                        '#3b82f6', // آبی
                        '#a78bfa', // بنفش
                        '#94a3b8', // خاکستری روشن
                        '#f9a8d4', // صورتی روشن
                        '#fdba74', // نارنجی روشن
                        '#86efac', // سبز روشن
                        '#93c5fd', // آبی روشن
                        '#60a5fa', // آبی روشن
                        '#c4b5fd', // بنفش روشن
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'تسویه کارمزد',
                'اجرای مصوبه',
                'مصوبه اعتباری',
                'صدور معرفی نامه',
                'صورتحساب',
                'قبول بنگاه',
                'درخواست مصوبه',
                'امضا و ابلاغ',
                'تایید مفاد تفاهم نامه',
                'تنظیم مفاد تفاهم نامه',
                'موافقت بنگاه',
                'اعلام شرایط',
                'درخواست اولیه',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => 'function(context) { return "میانگین زمان انتظار (ساعت): " + context.parsed.y; }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'ticks' => [
                        'stepSize' => 20,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => true,
        ];
    }
}

