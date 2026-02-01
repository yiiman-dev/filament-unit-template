<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * آیتم شخص دارنده حق امضا
 *
 * @property string|null $name نام شخص
 * @property string|null $title عنوان
 * @property string|null $nationalId کدملی
 * @property string|null $obligatoryStatus وضعیت اجباری
 * @property string|null $normalStatus وضعیت عادی
 */
readonly class FinnoTechAuthorizedSignatoriesHolderItemDto
{
    public function __construct(
        /** @var string|null نام شخص */
        public ?string $name,

        /** @var string|null عنوان */
        public ?string $title,

        /** @var string|null کدملی */
        public ?string $nationalId,

        /** @var string|null وضعیت اجباری */
        public ?string $obligatoryStatus,

        /** @var string|null وضعیت عادی */
        public ?string $normalStatus,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:             $data['name'] ?? null,
            title:            $data['title'] ?? null,
            nationalId:       isset($data['nationalId']) ? (string)$data['nationalId'] : null,
            obligatoryStatus: $data['obligatoryStatus'] ?? null,
            normalStatus:     $data['normalStatus'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'nationalId' => $this->nationalId,
            'obligatoryStatus' => $this->obligatoryStatus,
            'normalStatus' => $this->normalStatus,
        ];
    }

    public function isSuccess():bool
    {
        return true;
    }
}
