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
use Units\Enactment\Execution\Common\Models\EnactmentExecutionModel;
use Units\Enactment\Operation\Common\Models\EnactmentOperationModel;
use Units\Enactment\Request\Common\Models\EnactmentRequestModel;
use Units\FinanceRequest\Common\Models\FinanceRequestModel;
use Units\Memorandum\Operational\Common\Models\MemorandumOperationalModel;
use Units\Memorandum\Request\Common\Models\MemorandumRequestModel;

/**
 * Widget for displaying resource value by process as a donut chart
 */
class ResourceValueDonutChartWidget extends ChartWidget
{
    protected static ?string $heading = 'ارزش منابع بر اساس فرآیند';

    protected int | string | array $columnSpan = [
        'md' => 5,
        'xl' => 5,
    ]; // 5 قسمت از 12 قسمت (5/12)

    protected function getData(): array
    {
        $financeRequestAmount = FinanceRequestModel::query()->sum('amount');
        $memorandumRequestAmount = MemorandumRequestModel::query()->sum('amount');
        $memorandumOperationalAmount = MemorandumOperationalModel::query()->sum('total_amount');
        $enactmentRequestAmount = EnactmentRequestModel::query()->sum('total_amount');
        $enactmentOperationAmount = EnactmentOperationModel::query()->sum('total_amount');
        $executeAmount = EnactmentExecutionModel::query()->sum('amount');
        return [
            'datasets' => [
                [
                    'data' => [$financeRequestAmount, $memorandumRequestAmount, $memorandumOperationalAmount, $enactmentRequestAmount, $enactmentOperationAmount, $executeAmount],
                    'backgroundColor' => [
                        '#ec4899', // صورتی - درخواست اولیه
                        '#60a5fa', // آبی روشن - پیش رزرو تفاهم نامه
                        '#a78bfa', // بنفش روشن - رزرو تفاهم نامه
                        '#34d399', // سبز روشن - درخواست مصوبه
                        '#7c3aed', // بنفش تیره - دارای مصوبه
                        '#fbbf24', // زرد - اجرا شده
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'درخواست اولیه',
                'پیش رزرو تفاهم نامه',
                'رزرو تفاهم نامه',
                'درخواست مصوبه',
                'دارای مصوبه',
                'اجرا شده',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
            'responsive' => true,
            'maintainAspectRatio' => true,
        ];
    }
}

