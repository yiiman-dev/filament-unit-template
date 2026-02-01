<?php

namespace Units\Dashboard\Admin\Widgets;

use Enums\ProjectIconsEnum;
use Filament\Widgets\StatsOverviewWidget;
use Units\ContractTemplate\Common\Enums\FinancierStatusEnum;
use Units\FinanceRequest\Common\Enums\FinanceRequestStatusesEnum;
use Units\FinanceRequest\Common\Models\FinanceRequestModel;

class FinanceRequestAmountWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval='10s';
    protected function getStats(): array
    {
        $amount=FinanceRequestModel::query()
//            ->whereNot('reception_status')
            ->sum('amount');
        $chart_data=FinanceRequestModel::query()->groupBy(['created_at','amount'])->select('amount')->get()->pluck('amount')->toArray();
        $amount=number_format($amount);
        return [
            StatsOverviewWidget\Stat::make('منابع مالی درخواست شده', $amount.' میلیارد ریال')
            ->icon(ProjectIconsEnum::REVIEW->value)
                ->chart($chart_data)
            ->description('مجموع کل منابع مالی که بنگاه ها درخواست نموده اند')
            ->color('danger'),
        ];
    }

}
