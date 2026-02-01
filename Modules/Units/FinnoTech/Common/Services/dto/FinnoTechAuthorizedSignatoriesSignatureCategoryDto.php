<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * دسته‌بندی نوع امضا (شامل لیست دارندگان و روابط)
 *
 * @property FinnoTechAuthorizedSignatoriesHolderItemDto[] $holders دارندگان حق امضا
 * @property array $relationships همراه با (روابط)
 */
readonly class FinnoTechAuthorizedSignatoriesSignatureCategoryDto
{
    public function __construct(
        /** * دارندگان حق امضا
         * @var FinnoTechAuthorizedSignatoriesHolderItemDto[]
         */
        public array $holders,

        /** * همراه با (روابط)
         * @var array
         */
        public array $relationships,
    ) {}

    public static function fromArray(array $data): self
    {
        // مپ کردن لیست دارندگان امضا
        $rawHolders = $data['holders'] ?? [];
        if (!is_array($rawHolders)) {
            $rawHolders = [];
        }

        $holderObjects = array_map(
            fn($row) => FinnoTechAuthorizedSignatoriesHolderItemDto::fromArray($row),
            $rawHolders
        );

        return new self(
            holders:       $holderObjects,
            relationships: $data['relationships'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'holders' => array_map(fn($holder) => $holder->toArray(), $this->holders),
            'relationships' => $this->relationships,
        ];
    }

    public function isSuccess():bool
    {
        return true;
    }
}
