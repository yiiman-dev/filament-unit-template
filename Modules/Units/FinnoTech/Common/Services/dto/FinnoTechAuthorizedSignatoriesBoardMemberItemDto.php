<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * آیتم عضو هیئت مدیره (صاحبان امضا)
 *
 * @property string|null $companyId شناسه ملی شرکت
 * @property string|null $company نام شرکت
 * @property string|null $byNewsId شماره آگهی تعیین کننده این سمت
 * @property string|null $nationalId کدملی شخص
 * @property string|null $name نام و نام خانوادگی
 * @property string|null $gender جنسیت
 * @property string|null $pictureUrl تصویر شخص
 * @property string|null $mobile شماره موبایل
 * @property string|null $privateMobile شماره موبایل شخصی
 * @property string|null $email آدرس ایمیل
 * @property string|null $positionId شناسه سمت تخصیص داده شده
 * @property string|null $title سمت تخصیص داده شده
 * @property string|null $tagline یکی از سمت های شخص (تگ لاین)
 * @property string|null $firstRole اولین سمت (برای چند سمتی ها)
 * @property string|null $secondRole دومین سمت (برای چند سمتی ها)
 * @property string|null $startDate تاریخ شروع سمت
 * @property string|null $endDate تاریخ پایان سمت
 * @property string|null $duration مدت سمت (به سال)
 */
readonly class FinnoTechAuthorizedSignatoriesBoardMemberItemDto
{
    public function __construct(
        // --- شناسه ها و اطلاعات شرکت ---

        /** @var string|null شناسه ملی شرکت */
        public ?string $companyId,

        /** @var string|null نام شرکت */
        public ?string $company,

        /** @var string|null شماره آگهی تعیین کننده این سمت */
        public ?string $byNewsId,

        // --- اطلاعات هویتی شخص ---

        /** @var string|null کدملی شخص */
        public ?string $nationalId,

        /** @var string|null نام و نام خانوادگی */
        public ?string $name,

        /** @var string|null جنسیت */
        public ?string $gender,

        /** @var string|null تصویر شخص */
        public ?string $pictureUrl,

        // --- اطلاعات تماس ---

        /** @var string|null شماره موبایل */
        public ?string $mobile,

        /** @var string|null شماره موبایل شخصی */
        public ?string $privateMobile,

        /** @var string|null آدرس ایمیل */
        public ?string $email,

        // --- اطلاعات سمت و نقش ---

        /** @var string|null شناسه سمت تخصیص داده شده */
        public ?string $positionId,

        /** @var string|null سمت تخصیص داده شده */
        public ?string $title,

        /** @var string|null یکی از سمت های شخص (تگ لاین) */
        public ?string $tagline,

        /** @var string|null اولین سمت (برای چند سمتی ها) */
        public ?string $firstRole,

        /** @var string|null دومین سمت (برای چند سمتی ها) */
        public ?string $secondRole,

        // --- تاریخ و مدت ---

        /** @var string|null تاریخ شروع سمت */
        public ?string $startDate,

        /** @var string|null تاریخ پایان سمت */
        public ?string $endDate,

        /** @var string|null مدت سمت (به سال) */
        public ?string $duration,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            companyId:     isset($data['companyId']) ? (string)$data['companyId'] : null,
            company:       $data['company'] ?? null,
            byNewsId:      isset($data['byNewsId']) ? (string)$data['byNewsId'] : null,

            nationalId:    isset($data['nationalId']) ? (string)$data['nationalId'] : null,
            name:          $data['name'] ?? null,
            gender:        $data['gender'] ?? null,
            pictureUrl:    $data['pictureUrl'] ?? null,

            mobile:        $data['mobile'] ?? null,
            privateMobile: $data['privateMobile'] ?? null,
            email:         $data['email'] ?? null,

            positionId:    isset($data['positionId']) ? (string)$data['positionId'] : null,
            title:         $data['title'] ?? null,
            tagline:       $data['tagline'] ?? null,
            firstRole:     $data['firstRole'] ?? null,
            secondRole:    $data['secondRole'] ?? null,

            startDate:     $data['startDate'] ?? null,
            endDate:       $data['endDate'] ?? null,
            duration:      $data['duration'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'companyId' => $this->companyId,
            'company' => $this->company,
            'byNewsId' => $this->byNewsId,
            'nationalId' => $this->nationalId,
            'name' => $this->name,
            'gender' => $this->gender,
            'pictureUrl' => $this->pictureUrl,
            'mobile' => $this->mobile,
            'privateMobile' => $this->privateMobile,
            'email' => $this->email,
            'positionId' => $this->positionId,
            'title' => $this->title,
            'tagline' => $this->tagline,
            'firstRole' => $this->firstRole,
            'secondRole' => $this->secondRole,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'duration' => $this->duration,
        ];
    }

    public function isSuccess():bool
    {
        return true;
    }
}
