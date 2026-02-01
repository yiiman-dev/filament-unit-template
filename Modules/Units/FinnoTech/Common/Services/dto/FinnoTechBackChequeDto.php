<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * DTO اصلی استعلام چک‌های برگشتی
 * شامل اطلاعات صاحب حساب و لیست چک‌ها
 *
 * @property string|null $nid کد ملی کاربر
 * @property string|null $name نام و نام خانوادگی کاربر
 * @property FinnoTechBackChequeItemDto[] $chequeList لیست چک‌های برگشتی کاربر
 */
readonly class FinnoTechBackChequeDto
{
    public function __construct(
        /** @var string|null کد ملی کاربر */
        public ?string $nid,

        /** @var string|null نام و نام خانوادگی کاربر */
        public ?string $name,

        /** * لیست چک‌های برگشتی کاربر
         * @var FinnoTechBackChequeItemDto[]
         */
        public array $chequeList,
    ) {}

    public static function fromArray(array $data): self
    {
        // مدیریت تبدیل لیست چک‌ها
        $rawList = $data['chequeList'] ?? [];

        if (!is_array($rawList)) {
            $rawList = [];
        }

        $chequeListObjects = array_map(
            fn($row) => FinnoTechBackChequeItemDto::fromArray($row),
            $rawList
        );

        return new self(
            nid:        $data['nid'] ?? null,
            name:       $data['name'] ?? null,
            chequeList: $chequeListObjects
        );
    }

    public function isSuccess():bool
    {
        return true;
    }
}
