<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * DTO اصلی استعلام تسهیلات فینوتک
 * شامل لیست تسهیلات جاری کاربر
 *
 * @property string|null $user کد ملی کاربر یا شناسه ملی ده رقمی
 * @property string|null $name نام و نام خانوادگی کاربر
 * @property string|null $facilityTotalAmount مجموع مبلغ تسهیلات
 * @property string|null $facilityDebtTotalAmount مجموع تسهیلات مانده
 * @property string|null $facilityPastExpiredTotalAmount مانده سررسید گذشته (Past Expired)
 * @property string|null $facilityDeferredTotalAmount مانده معوق (Deferred)
 * @property string|null $facilitySuspiciousTotalAmount مانده مشکوک الوصول (Suspicious)
 * @property string|null $dishonored وضعیت بدحسابی
 * @property FinnoTechFacilityInquiryFacilityItemDto[] $facilityList لیست جزئیات تسهیلات مشتری
 */
readonly class FinnoTechFacilityInquiryDto
{
    public function __construct(
        // --- اطلاعات هویتی (Identity Info) ---

        /** @var string|null  کد ملی کاربر یا شناسه ملی ده رقمی */
        public ?string $user,

        /** @var string|null نام و نام خانوادگی کاربر */
        public ?string $name,

        // --- مبالغ کل (Total Amounts) ---

        /** @var string|null مجموع مبلغ تسهیلات */
        public ?string $facilityTotalAmount,

        /** @var string|null مجموع تسهیلات مانده */
        public ?string $facilityDebtTotalAmount,

        /** @var string|null مانده سررسید گذشته (Past Expired) */
        public ?string $facilityPastExpiredTotalAmount,

        /** @var string|null مانده معوق (Deferred) */
        public ?string $facilityDeferredTotalAmount,

        /** @var string|null مانده مشکوک الوصول (Suspicious) */
        public ?string $facilitySuspiciousTotalAmount,

        // --- وضعیت (Status) ---

        /** @var string|null وضعیت بدحسابی */
        public ?string $dishonored,

        // --- لیست تسهیلات (Nested Objects) ---

        /** * لیست جزئیات تسهیلات مشتری
         * @var FinnoTechFacilityInquiryFacilityItemDto[]
         */
        public array $facilityList,
    ) {}

    /**
     * متد کارخانه (Factory)
     * @param array $data آرایه خام پاسخ سرویس
     * @return self
     */
    public static function fromArray(array $data): self
    {
        // مدیریت تبدیل لیست تو در تو
        $rawList = $data['facilityList'] ?? [];

        // اطمینان از اینکه ورودی حتما آرایه است (جلوگیری از خطا در array_map)
        if (!is_array($rawList)) {
            $rawList = [];
        }

        $facilityList = array_map(
            fn($row) => FinnoTechFacilityInquiryFacilityItemDto::fromArray($row),
            $rawList
        );

        return new self(
            user:                           $data['user'] ?? null,
            name:                           $data['name'] ?? null,
            facilityTotalAmount:            $data['facilityTotalAmount'] ?? null,
            facilityDebtTotalAmount:        $data['facilityDebtTotalAmount'] ?? null,
            facilityPastExpiredTotalAmount: $data['facilityPastExpiredTotalAmount'] ?? null, // تایپوی داکیومنت اصلاح شد
            facilityDeferredTotalAmount:    $data['facilityDeferredTotalAmount'] ?? null,
            facilitySuspiciousTotalAmount:  $data['facilitySuspiciousTotalAmount'] ?? null,
            dishonored:                     $data['dishonored'] ?? null,
            facilityList:                   $facilityList
        );
    }

    public function isSuccess():bool
    {
        return true;
    }
}
