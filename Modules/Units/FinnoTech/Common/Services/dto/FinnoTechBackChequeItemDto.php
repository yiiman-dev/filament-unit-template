<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * آیتم چک برگشتی
 * شامل جزئیات کامل یک چک برگشت خورده
 *
 * @property string|null $accountNumber شماره حساب مربوط به چک
 * @property string|null $number شماره چک
 * @property string|null $id کد رهگیری چک
 * @property string|null $amount مبلغ چک برگشتی
 * @property string|null $bouncedAmount مبلغ برگشتی (طبق داکیومنت)
 * @property string|null $bankCode کد بانک صادر کننده چک
 * @property string|null $branchCode کد شعبه صادر کننده چک
 * @property string|null $branchDescription نام و شعبه بانک صادر کننده چک
 * @property string|null $dishonoringBranchName نام شعبه برگشت زننده
 * @property string|null $branchBounced کد شعبه برگشت زننده
 * @property string|null $dishonorReason دلیل برگشت چک
 * @property string|null $date تاریخ وصول چک (سررسید)
 * @property string|null $backDate تاریخ برگشت چک
 */
readonly class FinnoTechBackChequeItemDto
{
    public function __construct(
        // --- اطلاعات حساب و چک ---

        /** @var string|null شماره حساب مربوط به چک */
        public ?string $accountNumber,

        /** @var string|null شماره چک */
        public ?string $number,

        /** @var string|null کد رهگیری چک */
        public ?string $id,

        // --- مبالغ ---

        /** @var string|null مبلغ چک برگشتی */
        public ?string $amount,

        /** @var string|null مبلغ برگشتی (طبق داکیومنت) */
        public ?string $bouncedAmount,

        // --- اطلاعات شعبه و بانک ---

        /** @var string|null کد بانک صادر کننده چک */
        public ?string $bankCode,

        /** @var string|null کد شعبه صادر کننده چک */
        public ?string $branchCode,

        /** @var string|null نام و شعبه بانک صادر کننده چک */
        public ?string $branchDescription,

        // --- اطلاعات برگشت چک (Dishonor Info) ---

        /** @var string|null نام شعبه برگشت زننده */
        public ?string $dishonoringBranchName,

        /** @var string|null کد شعبه برگشت زننده */
        public ?string $branchBounced,

        /** @var string|null دلیل برگشت چک */
        public ?string $dishonorReason,

        // --- تاریخ‌ها ---

        /** @var string|null تاریخ وصول چک (سررسید) */
        public ?string $date,

        /** @var string|null تاریخ برگشت چک */
        public ?string $backDate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            accountNumber:         $data['accountNumber'] ?? null,
            number:                $data['number'] ?? null,
            id:                    $data['id'] ?? null,

            amount:                isset($data['amount']) ? (string)$data['amount'] : null,
            bouncedAmount:         isset($data['bouncedAmount']) ? (string)$data['bouncedAmount'] : null,

            bankCode:              isset($data['bankCode']) ? (string)$data['bankCode'] : null,
            branchCode:            $data['branchCode'] ?? null,
            branchDescription:     $data['branchDescription'] ?? null,

            dishonoringBranchName: $data['dishonoringBranchName'] ?? null,
            branchBounced:         $data['branchBounced'] ?? null,
            dishonorReason:        $data['dishonorReason'] ?? null,

            date:                  $data['date'] ?? null,
            backDate:              $data['backDate'] ?? null
        );
    }

    public function isSuccess():bool
    {
        return true;
    }
}
