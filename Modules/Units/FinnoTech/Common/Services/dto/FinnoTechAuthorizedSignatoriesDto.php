<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * DTO اصلی نتیجه سرویس صاحبان امضا
 *
 * @property string|null $newsId شماره آگهی روزنامه رسمی
 * @property string|null $companyTitle عنوان شرکت
 * @property string|null $signatureItemFullText متن کامل مورد امضا
 * @property array|null $allowedTopics اسناد مجاز برای امضا
 * @property string|null $newspaperDate تاریخ چاپ روزنامه رسمی
 * @property string|null $newsletterDate تاریخ نامه ثبت
 * @property string|null $lastNewspaperDate تاریخ آخرین تغییر روزنامه رسمی
 * @property FinnoTechAuthorizedSignatoriesSignholdersStatusDto|null $signholdersStatus دارندگان حق امضا
 * @property FinnoTechAuthorizedSignatoriesBoardMemberItemDto[] $boardMembers اعضای هیئت مدیره
 */
readonly class FinnoTechAuthorizedSignatoriesDto
{
    public function __construct(
        /** @var string|null شماره آگهی روزنامه رسمی */
        public ?string $newsId,

        /** @var string|null عنوان شرکت */
        public ?string $companyTitle,

        /** @var string|null متن کامل مورد امضا */
        public ?string $signatureItemFullText,

        /** @var array|null اسناد مجاز برای امضا */
        public array|string|null $allowedTopics,

        // --- تاریخ‌ها ---
        /** @var string|null تاریخ چاپ روزنامه رسمی */
        public ?string $newspaperDate,

        /** @var string|null تاریخ نامه ثبت */
        public ?string $newsletterDate,

        /** @var string|null تاریخ آخرین تغییر روزنامه رسمی */
        public ?string $lastNewspaperDate,

        // --- آبجکت‌های تو در تو ---

        /** @var FinnoTechAuthorizedSignatoriesSignholdersStatusDto|null دارندگان حق امضا */
        public ?FinnoTechAuthorizedSignatoriesSignholdersStatusDto $signholdersStatus,

        /** * اعضای هیئت مدیره
         * @var FinnoTechAuthorizedSignatoriesBoardMemberItemDto[]
         */
        public array $boardMembers,
    ) {}

    public static function fromArray(array $data): self
    {
        // مدیریت لیست اعضای هیئت مدیره
        $rawMembers = $data['boardMembers'] ?? [];
        if (!is_array($rawMembers)) {
            $rawMembers = [];
        }

        $boardMemberObjects = array_map(
            fn($row) => FinnoTechAuthorizedSignatoriesBoardMemberItemDto::fromArray($row),
            $rawMembers
        );

        // مدیریت وضعیت امضا
        $signholdersObj = isset($data['signholdersStatus'])
            ? FinnoTechAuthorizedSignatoriesSignholdersStatusDto::fromArray($data['signholdersStatus'])
            : null;

        return new self(
            newsId:                isset($data['newsId']) ? (string)$data['newsId'] : null,
            companyTitle:          $data['companyTitle'] ?? null,
            signatureItemFullText: $data['signatureItemFullText'] ?? null,
            allowedTopics:         $data['allowedTopics'] ?? null,

            newspaperDate:         $data['newspaperDate'] ?? null,
            newsletterDate:        $data['newsletterDate'] ?? null,
            lastNewspaperDate:     $data['lastNewspaperDate'] ?? null,

            signholdersStatus:     $signholdersObj,
            boardMembers:          $boardMemberObjects
        );
    }

    public function toArray(): array
    {
        return [
            'newsId' => $this->newsId,
            'companyTitle' => $this->companyTitle,
            'signatureItemFullText' => $this->signatureItemFullText,
            'allowedTopics' => $this->allowedTopics,
            'newspaperDate' => $this->newspaperDate,
            'newsletterDate' => $this->newsletterDate,
            'lastNewspaperDate' => $this->lastNewspaperDate,
            'signholdersStatus' => $this->signholdersStatus?->toArray(),
            'boardMembers' => array_map(fn($member) => $member->toArray(), $this->boardMembers),
        ];
    }

    public function isSuccess():bool
    {
        return true;
    }
}
