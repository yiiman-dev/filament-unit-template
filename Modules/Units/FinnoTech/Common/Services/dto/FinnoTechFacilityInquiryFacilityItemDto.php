<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * کلاس انتقال داده (DTO) برای آیتم‌های استعلام تسهیلات فینوتک
 *
 * @property string|null $bankCode کد بانک
 * @property string|null $branchCode شعبه بانک
 * @property string|null $branchDescription نام و شعبه بانک صادر کننده تسهیلات
 * @property string|null $pastExpiredAmount مانده سررسید گذشته
 * @property string|null $deferredAmount مانده معوقه
 * @property string|null $suspiciousAmount مانده مشکوک الوصول
 * @property string|null $debtorTotalAmount مانده کلی بدهی
 * @property string|null $originalAmount اصل مبلغ (amountOrginal)
 * @property string|null $benefitAmount مبلغ سود
 * @property string|null $type نوع قرارداد
 * @property string|null $facilityBankCode کد بانک تسهیلات
 * @property string|null $facilityBranchCode کد شعبه بانک تسهیلات
 * @property string|null $facilityBranch نام شعبه بانک تسهیلات
 * @property string|null $facilityRequestNo شماره درخواست
 * @property string|null $facilityRequestType نوع درخواست
 * @property string|null $facilityCurrencyCode کد ارز (مثلاً IRR)
 * @property string|null $facilityStatus وضعیت تسهیلات (مثلاً جاری)
 * @property string|null $facilityGroup دسته‌بندی تسهیلات (مثلاً اصلی)
 * @property string|null $facilityPastExpiredAmount مانده سررسید گذشته تسهیلات
 * @property string|null $facilityDeferredAmount مانده معوقه تسهیلات
 * @property string|null $facilitySuspiciousAmount مانده مشکوک الوصول تسهیلات
 * @property string|null $facilityDebtorTotalAmount مانده کل بدهی تسهیلات
 * @property string|null $facilityType نوع عقد قرارداد تسهیلات
 * @property string|null $facilityOriginalAmount اصل مبلغ تسهیلات
 * @property string|null $facilityBenefitAmount سود مبلغ تسهیلات
 * @property string|null $facilityAmountObligation مبلغ وجه التزام
 * @property string|null $facilitySetDate تاریخ تنظیم قرارداد (شمسی)
 * @property string|null $facilityEndDate تاریخ سررسید نهایی (شمسی)
 * @property string|null $facilityMoratoriumDate تاریخ مهلت/توقیف (Moratorium)
 */
readonly class FinnoTechFacilityInquiryFacilityItemDto
{
    public function __construct(
        // --- اطلاعات کلی بانک (General Bank Info) ---

        /** @var string|null کد بانک */
        public ?string $bankCode,

        /** @var string|null شعبه بانک */
        public ?string $branchCode,

        /** @var string|null نام و شعبه بانک صادر کننده تسهیلات */
        public ?string $branchDescription,

        // --- مبالغ کلی (General Amounts) ---

        /** @var string|null مانده سررسید گذشته */
        public ?string $pastExpiredAmount,

        /** @var string|null مانده معوقه */
        public ?string $deferredAmount,

        /** @var string|null مانده مشکوک الوصول */
        public ?string $suspiciousAmount,

        /** @var string|null مانده کلی بدهی */
        public ?string $debtorTotalAmount,

        /** @var string|null اصل مبلغ (amountOrginal) */
        public ?string $originalAmount,

        /** @var string|null مبلغ سود */
        public ?string $benefitAmount,

        /** @var string|null نوع قرارداد */
        public ?string $type,

        // --- اطلاعات اختصاصی تسهیلات (Facility Specific Info) ---

        /** @var string|null کد بانک تسهیلات */
        public ?string $facilityBankCode,

        /** @var string|null کد شعبه بانک تسهیلات */
        public ?string $facilityBranchCode,

        /** @var string|null نام شعبه بانک تسهیلات */
        public ?string $facilityBranch,

        /** @var string|null شماره درخواست */
        public ?string $facilityRequestNo,

        /** @var string|null نوع درخواست */
        public ?string $facilityRequestType,

        /** @var string|null کد ارز (مثلاً IRR) */
        public ?string $facilityCurrencyCode,

        /** @var string|null وضعیت تسهیلات (مثلاً جاری) */
        public ?string $facilityStatus,

        /** @var string|null دسته‌بندی تسهیلات (مثلاً اصلی) */
        public ?string $facilityGroup,

        // --- مبالغ تسهیلات (Facility Amounts) ---

        /** @var string|null مانده سررسید گذشته تسهیلات */
        public ?string $facilityPastExpiredAmount,

        /** @var string|null مانده معوقه تسهیلات */
        public ?string $facilityDeferredAmount,

        /** @var string|null مانده مشکوک الوصول تسهیلات */
        public ?string $facilitySuspiciousAmount,

        /** @var string|null مانده کل بدهی تسهیلات */
        public ?string $facilityDebtorTotalAmount,

        /** * نوع عقد قرارداد تسهیلات
         * * لیست کدها:
         * 10: قرض الحسنه | 11: مشارکت مدنی | 12: مشارکت حقوقی
         * 13: سرمایه گذاری مستقیم | 14: مضاربه | 15: معاملات سلف
         * 16: فروش اقساطی مواد اولیه | 17: فروش اقساطی ماشین‌آلات
         * 18: فروش اقساطی مسکن | 19: اجاره به شرط تملیک | 20: جعاله
         * 21: مزارعه | 22: مساقات | 23: خرید دین
         * 24: معاملات قدیم | 25: استصناع | 26: مرابحه
         * * @var string|null
         */
        public ?string $facilityType,

        /** @var string|null اصل مبلغ تسهیلات */
        public ?string $facilityOriginalAmount,

        /** @var string|null سود مبلغ تسهیلات */
        public ?string $facilityBenefitAmount,

        /** @var string|null مبلغ وجه التزام */
        public ?string $facilityAmountObligation,

        // --- تاریخ‌ها (Dates) ---

        /** @var string|null تاریخ تنظیم قرارداد (شمسی) */
        public ?string $facilitySetDate,

        /** @var string|null تاریخ سررسید نهایی (شمسی) */
        public ?string $facilityEndDate,

        /** @var string|null تاریخ مهلت/توقیف (Moratorium) */
        public ?string $facilityMoratoriumDate,
    ) {}

    /**
     * متد کارخانه (Factory) برای ساخت آبجکت از آرایه ورودی
     * * این متد کلیدهای نامرتب وب‌سرویس را به پراپرتی‌های استاندارد کلاس مپ می‌کند.
     * * @param array $data آرایه خام دریافتی از API
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            bankCode:                  $data['bankCode'] ?? null,
            branchCode:                $data['branchCode'] ?? null,
            branchDescription:         $data['branchDescription'] ?? null,

            pastExpiredAmount:         $data['pastExpiredAmount'] ?? null,
            deferredAmount:            $data['deferredAmount'] ?? null,
            suspiciousAmount:          $data['suspiciousAmount'] ?? null,
            debtorTotalAmount:         $data['debtorTotalAmount'] ?? null,
            originalAmount:            $data['amountOrginal'] ?? null, // اصلاح تایپوگرافی سمت سرویس
            benefitAmount:             $data['benefitAmount'] ?? null,
            type:                      $data['type'] ?? null,

            facilityBankCode:          $data['FacilityBankCode'] ?? null,
            facilityBranchCode:        $data['FacilityBranchCode'] ?? null,
            facilityBranch:            $data['FacilityBranch'] ?? null,
            facilityRequestNo:         $data['FacilityRequestNo'] ?? null,
            facilityRequestType:       $data['FacilityRequestType'] ?? null,
            facilityCurrencyCode:      $data['FacilityCurrencyCode'] ?? null,
            facilityStatus:            $data['FacilityStatus'] ?? null,
            facilityGroup:             $data['FacilityGroup'] ?? null,

            facilityPastExpiredAmount: $data['FacilityPastExpiredAmount'] ?? null,
            facilityDeferredAmount:    $data['FacilityDeferredAmount'] ?? null,
            facilitySuspiciousAmount:  $data['FacilitySuspiciousAmount'] ?? null,
            facilityDebtorTotalAmount: $data['FacilityDebtorTotalAmount'] ?? null,
            facilityType:              $data['FacilityType'] ?? null,
            facilityOriginalAmount:    $data['FacilityAmountOrginal'] ?? null,
            facilityBenefitAmount:     $data['FacilityBenefitAmount'] ?? null,
            facilityAmountObligation:  $data['FacilityAmountObligation'] ?? null,

            facilitySetDate:           $data['FacilitySetDate'] ?? null,
            facilityEndDate:           $data['FacilityEndDate'] ?? null,
            facilityMoratoriumDate:    $data['FacilityMoratoriumDate'] ?? null
        );
    }

    public function isSuccess():bool
    {
        return true;
    }
}
