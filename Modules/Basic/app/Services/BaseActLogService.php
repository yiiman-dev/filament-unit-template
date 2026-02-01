<?php

namespace Modules\Basic\Services;

use Filament\Facades\Filament;
use Illuminate\Contracts\Database\Query\Builder;
use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\Services\Contracts\ActLogs\UserActContract;

class BaseActLogService extends BaseService
{
//    use UserActContract;

    /**
     * این تابع برای ترجمه ی مقادیر لاتین اکشن ها به فارسی یا زبان های دیگر با استفاده از فایل های ترجمه استفاده می شود
     *
     * با این روش کاربران میتوانند درک بهتری از آنچه رخ داده است پیدا کنند.
     * @param $action_name
     * @return string
     */
    public static function _($action_name): string
    {
        switch (filament()->getCurrentPanel()->getId()){
            case 'admin':
                return trans('admin_acts::acts.' . $action_name);
            case 'manage':
                return trans('manage_acts::acts.' . $action_name);
            case 'my':
                return trans('my_acts::acts.' . $action_name);
        }
    }


    protected static string $model;

    public function login(): void
    {
        $this->actLog(action: 'success_login', type: 'login');
    }


    /**
     * دریافت لاگ‌های سیستم با فیلتر‌های مختلف
     *
     * @return Builder
     */
    public function getLogs($normalized_phone_number = null)
    {
        if (empty($normalized_phone_number)) {
            $normalized_phone_number = Filament::auth()->user()->phone_number;
        }
        return static::$model::query()->where('actor_number', $normalized_phone_number);
    }

    /**
     * ثبت لاگ جدید
     *
     * @param string $action نوع عملیات
     * @param string $type نوع تغییر
     * @param string|null $targetUrl آدرس هدف
     * @param string|null $targetTitle عنوان هدف
     * @param array|null $details جزئیات تغییرات
     * @param string|null $remote_actor_number در صورتی که تغییرات از یک سیستم ریموت در حال انجام است٬ باید این مقدار از وب سرویس دریافت و در این پراپرتی قرار داده شود تا شماره ی کاربری که اکت را روی این مدل انجام داده است مشخص شود
     * @return self
     */
    public function actLog(
        string $action,
        string $type,
        ?string $targetUrl = null,
        ?string $targetTitle = null,
        ?array $details = null,
        ?string $remote_actor_number=null
    ): self {
        // محاسبه هش رکورد قبلی
        $previousLog = static::$model::orderBy('created_at', 'desc')->first();
        $previousHash = $previousLog ? $previousLog->hash : null;



        $actor_number='';
        if (empty(Filament::auth()->user()->phone_number)){
            if (!empty($remote_actor_number)){
                $actor_number=$remote_actor_number;
            }
        }else{
            $actor_number=Filament::auth()->user()->phone_number;
        }
        // محاسبه هش فعلی
        $currentData = [
            'action' => !empty($action) ? $action : null,
            'type' => !empty($type) ? $type : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'target_url' => !empty($targetUrl) ? $targetUrl : null,
            'target_title' => !empty($targetTitle) ? $targetTitle : null,
            'details' => !empty($details) ? $details : null,
//            'created_at' => date('Y-m-d H:i:s'),
            'actor_number' => $actor_number,
            'previous_hash' => !empty($previousHash) ? $previousHash : null,
        ];

        $hash = hash('sha256', json_encode($currentData));

        // ایجاد رکورد لاگ
        static::$model::create(
            [
                ...$currentData,
                'hash' => $hash
            ]
        );
        return $this;
    }

    /**
     * بررسی صحت زنجیره هش‌ها
     *
     * @return array
     */
    public function verifyChain(): array
    {
        $logs = static::$model::orderBy('created_at', 'ASC')->get();
        $results = [
            'is_valid' => true,
            'errors' => []
        ];

        if ($logs->isEmpty()) {
            return $results;
        }

        $previousHash = null;

        foreach ($logs as $index => $log) {

            $currentData = [
                'action' => $log->action,
                'type' => $log->type,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'target_url' => $log->target_url,
                'target_title' => $log->target_title,
                'details' => $log->details,
//                'created_at' => $log->created_at,
                'actor_number' => $log->actor_number,
                'previous_hash' => $previousHash,
            ];

            $calculatedHash = hash('sha256', json_encode($currentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            if ($calculatedHash !== $log->hash) {
                $results['is_valid'] = false;
                $results['errors'][] = [
                    'type' => 'hash_mismatch',
                    'index' => $index,
                    'calculated_hash' => $calculatedHash,
                    'stored_hash' => $log->hash,
                    'data' => $currentData
                ];
            }

            if ($log->previous_hash !== $previousHash) {
                $results['is_valid'] = false;
                $results['errors'][] = [
                    'type' => 'chain_break',
                    'index' => $index,
                    'expected_previous_hash' => $previousHash,
                    'actual_previous_hash' => $log->previous_hash
                ];
            }

            $previousHash = $log->hash;
        }

        return $results;
    }
}
