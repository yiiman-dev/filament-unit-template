<?php

namespace Units\FinnoTech\Common\Services\dto;

/**
 * خطای بازگشتی از فینوتک
 *
 */
readonly class FinnoTechErrorDto
{
    public function __construct(
        public string $responseCode,
        public string $status,
        public string|int $code,
        public string $message,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            responseCode:$data['responseCode'],
            status: $data['status'],
            code: $data["error"]['code'],
            message: $data["error"]['message']
        );
    }

    public function toArray(): array
    {
        return [
            'responseCode'=>$this->responseCode,
            'status' => $this->status,
            'message' => $this->message,
            'code' => $this->code,
        ];
    }

    public function isSuccess():bool
    {
        return false;
    }
}
