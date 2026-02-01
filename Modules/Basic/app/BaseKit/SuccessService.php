<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:42 PM
 */

namespace Modules\Basic\BaseKit;

use Illuminate\Support\Arr;

class SuccessService
{
    protected array $data;
    protected string $message;

    public function __construct(array $data, string $message)
    {
        $this->data = $data;
        $this->message = $message;
    }


    /**
     * Get an item from data property using 'dot' notation.
     * @param $key
     * @return array
     */
    public function getData($key=null): mixed
    {
        if (!empty($key)){
            return Arr::get($this->data, $key);
        }
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
