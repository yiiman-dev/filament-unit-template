<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * آیتم آگهی روزنامه رسمی شرکت
 *
 * @property int|null $newsId شماره آگهی روزنامه رسمی
 * @property string|null $title عنوان آگهی
 * @property string|null $description متن آگهی
 * @property string|null $companyId شناسه ملی شرکت
 * @property float|null $capitalTo سرمایه تغییر یافته در این آگهی
 * @property string|null $newspaperDate تاریخ چاپ روزنامه رسمی
 * @property string|null $newsletterDate تاریخ نامه ثبت
 * @property string|null $newspaperNumber شماره روزنامه
 * @property string|null $newspaperCity شهر روزنامه
 * @property int|null $pageNumber شماره صفحه روزنامه رسمی
 * @property string|null $indicatorNumber شماره اندیکاتور
 */
readonly class FinnoTechCorporateJournalItemDto
{
    public function __construct(
        /** @var int|null شماره آگهی روزنامه رسمی */
        public ?int $newsId,

        /** @var string|null عنوان آگهی */
        public ?string $title,

        /** @var string|null متن آگهی */
        public ?string $description,

        /** @var string|null شناسه ملی شرکت */
        public ?string $companyId,

        /** @var float|null سرمایه تغییر یافته در این آگهی */
        public ?float $capitalTo,

        /** @var string|null تاریخ چاپ روزنامه رسمی */
        public ?string $newspaperDate,

        /** @var string|null تاریخ نامه ثبت */
        public ?string $newsletterDate,

        /** @var string|null شماره روزنامه */
        public ?string $newspaperNumber,

        /** @var string|null شهر روزنامه */
        public ?string $newspaperCity,

        /** @var int|null شماره صفحه روزنامه رسمی */
        public ?int $pageNumber,

        /** @var string|null شماره اندیکاتور */
        public ?string $indicatorNumber,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            newsId: $data['newsId'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            companyId: isset($data['companyId']) ? (string)$data['companyId'] : null,
            capitalTo: isset($data['capitalTo']) ? (float)$data['capitalTo'] : null,
            newspaperDate: $data['newspaperDate'] ?? null,
            newsletterDate: $data['newsletterDate'] ?? null,
            newspaperNumber: $data['newspaperNumber'] ?? null,
            newspaperCity: $data['newspaperCity'] ?? null,
            pageNumber: $data['pageNumber'] ?? null,
            indicatorNumber: $data['indicatorNumber'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'newsId' => $this->newsId,
            'title' => $this->title,
            'description' => $this->description,
            'companyId' => $this->companyId,
            'capitalTo' => $this->capitalTo,
            'newspaperDate' => $this->newspaperDate,
            'newsletterDate' => $this->newsletterDate,
            'newspaperNumber' => $this->newspaperNumber,
            'newspaperCity' => $this->newspaperCity,
            'pageNumber' => $this->pageNumber,
            'indicatorNumber' => $this->indicatorNumber,
        ];
    }

    public function isSuccess():bool
    {
        return true;
    }
}
