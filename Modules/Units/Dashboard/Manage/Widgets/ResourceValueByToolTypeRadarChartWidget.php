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
 * Widget for displaying resource value by tool type as a radar chart
 */
class ResourceValueByToolTypeRadarChartWidget extends ChartWidget
{
    protected static ?string $heading = 'ارزش منابع بر اساس نوع ابزار';

    protected int | string | array $columnSpan = [
        'md' => 5,
        'xl' => 5,
    ]; // 6 قسمت از 12 قسمت (1/2)

    protected function getData(): array
    {
        // داده‌های استاتیک - می‌توانید بعداً با دیتابیس جایگزین کنید
        return [
            'datasets' => [
                [
                    'label' => 'سری 1',
                    'data' => [650, 300, 400, 100, 350, 600],
                    'backgroundColor' => 'rgba(139, 92, 246, 0.2)', // بنفش با شفافیت
                    'borderColor' => 'rgba(139, 92, 246, 1)', // بنفش
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgba(139, 92, 246, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(139, 92, 246, 1)',
                ],
                [
                    'label' => 'سری 2',
                    'data' => [500, 250, 350, 200, 300, 400],
                    'backgroundColor' => 'rgba(236, 72, 153, 0.2)', // صورتی با شفافیت
                    'borderColor' => 'rgba(236, 72, 153, 1)', // صورتی
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgba(236, 72, 153, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(236, 72, 153, 1)',
                ],
                [
                    'label' => 'سری 3',
                    'data' => [400, 200, 300, 150, 450, 350],
                    'backgroundColor' => 'rgba(134, 239, 172, 0.2)', // سبز روشن با شفافیت
                    'borderColor' => 'rgba(134, 239, 172, 1)', // سبز روشن
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgba(134, 239, 172, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(134, 239, 172, 1)',
                ],
            ],
            'labels' => [
                'درخواست اولیه',
                'پیش رزرو تفاهم نامه',
                'رزرو تفاهم نامه',
                'رزرو تسهیلات گیرنده',
                'دارای مصوبه',
                'اجرا شده',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'min' => 0,
                    'max' => 700,
                    'ticks' => [
                        'stepSize' => 100,
                    ],
                    'pointLabels' => [
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => true,
        ];
    }
}

