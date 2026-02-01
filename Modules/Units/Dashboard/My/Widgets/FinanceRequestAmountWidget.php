<?php

namespace Units\Dashboard\My\Widgets;

use Enums\ProjectIconsEnum;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget;
use Modules\Basic\Helpers\Helper;
use Units\ContractTemplate\Common\Enums\FinancierStatusEnum;
use Units\FinanceRequest\Common\Enums\FinanceRequestStatusesEnum;
use Units\FinanceRequest\Common\Models\FinanceRequestModel;
use Units\Memorandum\Request\Common\Models\MemorandumRequestModel;
use Units\Memorandum\Operational\Common\Models\MemorandumOperationalModel;
class FinanceRequestAmountWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $amount = FinanceRequestModel::query()
            ->where('corporate_national_code', Helper::current_user_corporate_national_code())
//            ->whereNot('reception_status')
            ->sum('amount');
        $chart_data = FinanceRequestModel::query()->where(
            'corporate_national_code',
            Helper::current_user_corporate_national_code()
        )->groupBy(['created_at', 'amount'])->select('amount')->get()->pluck('amount')->toArray();
        $amount = number_format($amount);


        $amountMemorandum = MemorandumRequestModel::query()
            ->where('corporate_national_code', Helper::current_user_corporate_national_code())
//            ->whereNot('reception_status')
            ->sum('amount');
        $MemorandumChart_data = MemorandumRequestModel::query()->where(
            'corporate_national_code',
            Helper::current_user_corporate_national_code()
        )->groupBy(['created_at', 'amount'])->select('amount')->get()->pluck('amount')->toArray();
        $amountMemorandum = number_format($amountMemorandum);


        $amountMemorandumOperational = MemorandumOperationalModel::class::query()
            ->where('src_corporate_national_code', Helper::current_user_corporate_national_code())
//            ->whereNot('reception_status')
            ->sum('total_amount');
        $MemorandumOperationalChart_data = MemorandumOperationalModel::query()->where(
            'src_corporate_national_code',
            Helper::current_user_corporate_national_code()
        )->groupBy(['created_at', 'total_amount'])->select('total_amount')->get()->pluck('total_amount')->toArray();
        $amountMemorandumOperational = number_format($amountMemorandumOperational);


        return
            $amount>0?
            [
            StatsOverviewWidget\Stat::make('منابع مالی درخواست شده', $amount . ' میلیارد ریال')
                ->icon(ProjectIconsEnum::REVIEW->value)
                ->chart($chart_data)
                ->description('مجموع کل منابع مالی که بنگاه شما درخواست نموده است')
                ->color(Color::Indigo),

            StatsOverviewWidget\Stat::make('تفاهم نامه های درخواست شده', $amountMemorandum . ' میلیارد ریال')
                ->icon(ProjectIconsEnum::REVIEW->value)
                ->chart($MemorandumChart_data)
                ->description('مجموع کل تفاهم نامه هایی که بنگاه شما درخواست نموده است')
                ->color(Color::Lime),
            StatsOverviewWidget\Stat::make('تفاهم نامه های عملیاتی', $amountMemorandumOperational . ' میلیارد ریال')
                ->icon(ProjectIconsEnum::REVIEW->value)
                ->chart($MemorandumOperationalChart_data)
                ->description('مجموع کل تفاهم نامه های عملیاتی ( جاری )')
                ->color(Color::Sky),


        ]:[];
    }

}
