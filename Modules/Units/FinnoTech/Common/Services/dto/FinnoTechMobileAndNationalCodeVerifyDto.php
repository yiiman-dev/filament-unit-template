<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * DTO برای تأیید موبایل و کد ملی
 * این کلاس اطلاعات مربوط به نتیجه تأیید موبایل و کد ملی را نگه‌داری می‌کند
 *
 * @property bool $isValid وضعیت معتبر بودن تأیید
 */
class FinnoTechMobileAndNationalCodeVerifyDto implements \JsonSerializable
{

    public function __construct(
        public bool $isValid
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'isValid' => $this->isValid,
        ];
    }

    public static function fromArray(array $data): self {
        return new self(
            isValid: (bool)$data['isValid'],
        );
    }

    public function getIsValid():bool
    {
        return $this->isValid;
    }

    public function isSuccess():bool
    {
        return true;
    }
}
