<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * وضعیت دارندگان حق امضا (شامل دسته‌های مختلف)
 *
 * @property FinnoTechAuthorizedSignatoriesSignatureCategoryDto|null $obligatorySignature دارندگان حق امضا- اجباری
 * @property FinnoTechAuthorizedSignatoriesSignatureCategoryDto|null $normalSignature دارندگان حق امضا- عادی
 * @property FinnoTechAuthorizedSignatoriesSignatureCategoryDto|null $obligatoryAndNormalSignature دارندگان حق امضا اجباری و عادی
 */
readonly class FinnoTechAuthorizedSignatoriesSignholdersStatusDto
{
    public function __construct(
        /** @var FinnoTechAuthorizedSignatoriesSignatureCategoryDto|null دارندگان حق امضا- اجباری */
        public ?FinnoTechAuthorizedSignatoriesSignatureCategoryDto $obligatorySignature,

        /** @var FinnoTechAuthorizedSignatoriesSignatureCategoryDto|null دارندگان حق امضا- عادی */
        public ?FinnoTechAuthorizedSignatoriesSignatureCategoryDto $normalSignature,

        /** @var FinnoTechAuthorizedSignatoriesSignatureCategoryDto|null دارندگان حق امضا اجباری و عادی */
        public ?FinnoTechAuthorizedSignatoriesSignatureCategoryDto $obligatoryAndNormalSignature,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            obligatorySignature:          isset($data['obligatorySignature']) ? FinnoTechAuthorizedSignatoriesSignatureCategoryDto::fromArray($data['obligatorySignature']) : null,
            normalSignature:              isset($data['normalSignature']) ? FinnoTechAuthorizedSignatoriesSignatureCategoryDto::fromArray($data['normalSignature']) : null,
            obligatoryAndNormalSignature: isset($data['obligatoryAndNormalSignature']) ? FinnoTechAuthorizedSignatoriesSignatureCategoryDto::fromArray($data['obligatoryAndNormalSignature']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'obligatorySignature' => $this->obligatorySignature?->toArray(),
            'normalSignature' => $this->normalSignature?->toArray(),
            'obligatoryAndNormalSignature' => $this->obligatoryAndNormalSignature?->toArray(),
        ];
    }

    public function isSuccess():bool
    {
        return true;
    }
}
