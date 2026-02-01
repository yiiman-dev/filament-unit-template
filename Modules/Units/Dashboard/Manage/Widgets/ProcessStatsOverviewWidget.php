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

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Units\BankLetter\Common\Models\LetterModel;
use Units\Enactment\Execution\Common\Models\EnactmentExecutionModel;
use Units\Enactment\Operation\Common\Models\EnactmentOperationModel;
use Units\Enactment\Request\Common\Models\EnactmentRequestModel;
use Units\FinanceRequest\Common\Models\FinanceRequestModel;
use Units\Memorandum\Operational\Common\Models\MemorandumOperationalModel;
use Units\Memorandum\Request\Common\Models\MemorandumRequestModel;
use Units\Memorandum\Request\Common\Models\MemorandumSigningModel;

/**
 * Widget for displaying process statistics with metric cards
 */
class ProcessStatsOverviewWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = [
        'md' => 7,
        'xl' => 7,
    ]; // 7 قسمت از 12 قسمت (7/12)

    /**
     * Get the number of columns for the stats cards
     *
     * @return int
     */
    protected function getColumns(): int
    {
        return 2; // 2 کارت در هر ردیف
    }

    /**
     * Get daily chart data for the last 30 days (29 days + today)
     *
     * @param Builder $query
     * @param string $amountField
     * @return array
     */
    protected function getDailyChartData(Builder $query, string $amountField = 'amount'): array
    {
        $today = Carbon::today();
        $startDate = $today->copy()->subDays(29)->startOfDay(); // 30 روز (29 روز گذشته + امروز)
        $endDate = $today->copy()->endOfDay();

        $dailyData = $query
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->selectRaw("DATE(created_at) as date, COALESCE(SUM({$amountField}), 0) as total")
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                // تبدیل تاریخ به فرمت Y-m-d برای تطبیق
                $date = $item->date instanceof \DateTime
                    ? $item->date->format('Y-m-d')
                    : Carbon::parse($item->date)->format('Y-m-d');
                return [$date => (float)$item->total];
            })
            ->toArray();

        // ساخت آرایه برای 30 روز
        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            $chartData[] = (float)($dailyData[$date] ?? 0);
        }

        return $chartData;
    }

    /**
     * Calculate percentage change based on today vs average daily
     *
     * @param Builder $query
     * @param string $amountField
     * @return array ['percentage' => float, 'isIncrease' => bool]
     */
    protected function calculatePercentageChange(Builder $query, string $amountField = 'amount'): array
    {
        $today = Carbon::today();

        // 30 روز گذشته بدون امروز
        $startDate = $today->copy()->subDays(30)->startOfDay();
        $endDate = $today->copy()->subDay()->endOfDay();

        $totalAmount = (clone $query)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum($amountField) ?? 0;

        $averageDaily = $totalAmount > 0 ? ($totalAmount / 30) : 0;

        // مقدار امروز
        $todayValue = (clone $query)
            ->whereBetween('created_at', [
                $today->copy()->startOfDay(),
                $today->copy()->endOfDay()
            ])
            ->sum($amountField) ?? 0;

        if ($averageDaily == 0) {
            $percentage = $todayValue > 0 ? 100 : 0;
        } else {
            $percentage = (($todayValue - $averageDaily) / $averageDaily) * 100;
        }

        return [
            'percentage' => round(abs($percentage)),
            'isIncrease' => $percentage >= 0,
        ];
    }

    protected function getStats(): array
    {

        // درخواست اولیه
        $financeRequest = FinanceRequestModel::query()->count();
        $financeRequestAmount = FinanceRequestModel::query()->sum('amount');
        $financeRequestChart = $this->getDailyChartData(FinanceRequestModel::query(), 'amount');
        $financeRequestPercentage = $this->calculatePercentageChange(FinanceRequestModel::query(), 'amount');

        // پیش رزرو تفاهم نامه
        $memorandumRequest = MemorandumRequestModel::query()->count();
        $memorandumRequestAmount = MemorandumRequestModel::query()->sum('amount');
        $memorandumRequestChart = $this->getDailyChartData(MemorandumRequestModel::query(), 'amount');
        $memorandumRequestPercentage = $this->calculatePercentageChange(MemorandumRequestModel::query(), 'amount');

        // رزرو تفاهم نامه
        $memorandumOperationalModel = MemorandumOperationalModel::query()->count();
        $memorandumOperationalAmount = MemorandumOperationalModel::query()->sum('total_amount');
        $memorandumOperationalChart = $this->getDailyChartData(MemorandumOperationalModel::query(), 'total_amount');
        $memorandumOperationalPercentage = $this->calculatePercentageChange(MemorandumOperationalModel::query(), 'total_amount');


        // درخواست مصوبه
        $enactmentRequest = EnactmentRequestModel::query()->count();
        $enactmentRequestAmount = EnactmentRequestModel::query()->sum('total_amount');
        $enactmentRequestChart = $this->getDailyChartData(EnactmentRequestModel::query(), 'total_amount');
        $enactmentRequestPercentage = $this->calculatePercentageChange(EnactmentRequestModel::query(), 'total_amount');

        // دارای مصوبه
        $enactmentOperation = EnactmentOperationModel::query()->count();
        $enactmentOperationAmount = EnactmentOperationModel::query()->sum('total_amount');
        $enactmentOperationChart = $this->getDailyChartData(EnactmentOperationModel::query(), 'total_amount');
        $enactmentOperationPercentage = $this->calculatePercentageChange(EnactmentOperationModel::query(), 'total_amount');

        // اجراشده
        $execute = EnactmentExecutionModel::query()->count();
        $executeAmount = EnactmentExecutionModel::query()->sum('amount');
        $executeChart = $this->getDailyChartData(EnactmentExecutionModel::query(), 'amount');
        $executePercentage = $this->calculatePercentageChange(EnactmentExecutionModel::query(), 'amount');

        return [
            Stat::make('درخواست اولیه', number_format($financeRequestAmount))
                ->description("تعداد: {$financeRequest} • {$financeRequestPercentage['percentage']}% " . ($financeRequestPercentage['isIncrease'] ? 'افزایش' : 'کاهش'))
                ->descriptionIcon($financeRequestPercentage['isIncrease'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($financeRequestPercentage['isIncrease'] ? 'success' : 'danger')
                ->chart($financeRequestChart)
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('پیش رزرو تفاهم نامه', number_format($memorandumRequestAmount))
                ->description("تعداد: {$memorandumRequest} • {$memorandumRequestPercentage['percentage']}% " . ($memorandumRequestPercentage['isIncrease'] ? 'افزایش' : 'کاهش'))
                ->descriptionIcon($memorandumRequestPercentage['isIncrease'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($memorandumRequestPercentage['isIncrease'] ? 'success' : 'danger')
                ->chart($memorandumRequestChart)
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('رزرو تفاهم نامه', number_format($memorandumOperationalAmount))
                ->description("تعداد: {$memorandumOperationalModel} • {$memorandumOperationalPercentage['percentage']}% " . ($memorandumOperationalPercentage['isIncrease'] ? 'افزایش' : 'کاهش'))
                ->descriptionIcon($memorandumOperationalPercentage['isIncrease'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($memorandumOperationalPercentage['isIncrease'] ? 'success' : 'danger')
                ->chart($memorandumOperationalChart)
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('درخواست مصوبه', number_format($enactmentRequestAmount))
                ->description("تعداد: {$enactmentRequest} • {$enactmentRequestPercentage['percentage']}% " . ($enactmentRequestPercentage['isIncrease'] ? 'افزایش' : 'کاهش'))
                ->descriptionIcon($enactmentRequestPercentage['isIncrease'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($enactmentRequestPercentage['isIncrease'] ? 'success' : 'danger')
                ->chart($enactmentRequestChart)
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('دارای مصوبه', number_format($enactmentOperationAmount))
                ->description("تعداد: {$enactmentOperation} • {$enactmentOperationPercentage['percentage']}% " . ($enactmentOperationPercentage['isIncrease'] ? 'افزایش' : 'کاهش'))
                ->descriptionIcon($enactmentOperationPercentage['isIncrease'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($enactmentOperationPercentage['isIncrease'] ? 'success' : 'danger')
                ->chart($enactmentOperationChart)
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('اجراشده', number_format($executeAmount))
                ->description("تعداد: {$execute} • {$executePercentage['percentage']}% " . ($executePercentage['isIncrease'] ? 'افزایش' : 'کاهش'))
                ->descriptionIcon($executePercentage['isIncrease'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($executePercentage['isIncrease'] ? 'success' : 'danger')
                ->chart($executeChart)
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }
}

