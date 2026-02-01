<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/26/25, 11:38 AM
 */

namespace Modules\Basic\Observers\Contracts;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * با توجه به اینکه کلاس های آبسرور در مدل ها در هر رویداد داده های پراپرتی کلاس آبسرور را خالی میکنند
 *
 * ٬ برای پاس دادن مقادیر موقتی بین رویداد ها از این رشته توابع استفاده کنید که باید به کلاس آبسرور الصاق شوند
 */
trait ObserverCacheTrait
{
    /**
     * تولید کلید کش یکتا بر اساس Observer، مدل، کاربر و رویداد
     *
     * @param object $model مدل Eloquent
     * @param string|null $eventName نام ایونت (مثلا creating, created)
     * @return string
     */
    protected function getCacheKey(object $model, ?string $eventName = null): string
    {
        $observerClass = static::class; // نام کلاس Observer که این Trait را استفاده می‌کند
        $modelClass = get_class($model);
        $primaryKey = $model->getKeyName();
        $primaryValue = $model->getAttribute($primaryKey);
        $userId = Auth::id() ?? session()->getId() ?? 'guest';

        $parts = [
            'observer_cache',
            $observerClass,
            $modelClass,
            $primaryKey,
            $primaryValue,
            $userId,
        ];

        if ($eventName) {
            $parts[] = $eventName;
        }

        // کلید را با علامت ":" به هم متصل می‌کنیم
        return implode(':', $parts);
    }

    /**
     * ذخیره داده در کش با TTL مشخص بر حسب ثانیه
     *
     * @param object $model
     * @param mixed $value
     * @param int $ttlSeconds مدت زمان نگهداری داده در کش به ثانیه
     * @param string|null $eventName
     * @return void
     */
    protected function cachePut(object $model, $value, int $ttlSeconds = 300, ?string $eventName = null): void
    {
        $key = $this->getCacheKey($model, $eventName);
        Cache::put($key, $value, $ttlSeconds);
    }

    /**
     * بازیابی داده از کش
     *
     * @param object $model
     * @param string|null $eventName
     * @return mixed|null
     */
    protected function cacheGet(object $model, ?string $eventName = null)
    {
        $key = $this->getCacheKey($model, $eventName);
        return Cache::get($key);
    }

    /**
     * حذف داده از کش
     *
     * @param object $model
     * @param string|null $eventName
     * @return void
     */
    protected function cacheForget(object $model, ?string $eventName = null): void
    {
        $key = $this->getCacheKey($model, $eventName);
        Cache::forget($key);
    }
}
