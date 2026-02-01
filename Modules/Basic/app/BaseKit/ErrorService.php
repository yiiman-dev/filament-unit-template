<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:44 PM
 */

namespace Modules\Basic\BaseKit;

use function PHPUnit\Framework\isString;

class ErrorService
{
    public array|string $data;
    public string $message;

    public function __construct(array|string $data, string $message)
    {
        $this->data = $data;
        $this->message = is_string($data)?$data:$message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
