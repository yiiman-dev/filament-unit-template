<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * DTO اصلی نتیجه سرویس آگهی های روزنامه رسمی شرکت
 *
 * @property array $result لیست آگهی های روزنامه رسمی
 * @property string $responseCode کد پاسخ دریافتی
 * @property string $trackId کد پیگیری که در فراخوانی سرویس فرستاده شده است
 * @property string $status وضعیت DONE/FAILED
 */
readonly class FinnoTechCorporateJournalDto
{
    public function __construct(
        /** * لیست آگهی های روزنامه رسمی
         * @var FinnoTechCorporateJournalItemDto[]
         */
        public array $result,

        /** @var string کد پاسخ دریافتی */
        public string $responseCode,

        /** @var string کد پیگیری که در فراخوانی سرویس فرستاده شده است */
        public string $trackId,

        /** @var string وضعیت DONE/FAILED */
        public string $status,
    ) {}

    /**
     * متد کارخانه (Factory)
     * @param array $data آرایه خام پاسخ سرویس
     * @return self
     */
    public static function fromArray(array $data): self
    {
        // مدیریت تبدیل لیست تو در تو
        $rawList = $data['result'] ?? [];

        // اطمینان از اینکه ورودی حتما آرایه است (جلوگیری از خطا در array_map)
        if (!is_array($rawList)) {
            $rawList = [];
        }

        $result = array_map(
            fn($row) => FinnoTechCorporateJournalItemDto::fromArray($row),
            $rawList
        );

        return new self(
            result: $result,
            responseCode: $data['responseCode'] ?? '',
            trackId: $data['trackId'] ?? '',
            status: $data['status'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'result' => array_map(fn($item) => $item->toArray(), $this->result),
            'responseCode' => $this->responseCode,
            'trackId' => $this->trackId,
            'status' => $this->status,
        ];
    }

    public function isSuccess():bool
    {
        return true;
    }
}
