<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/13/25, 10:12 AM
 */

namespace Modules\Basic\BaseKit;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class BaseDTO implements Arrayable
{
    public function toArray(
        bool $convertToSnakeCase = false,
    ): array {
        return $this->dto_to_array(
            dto: $this,
            convertToSnakeCase: $convertToSnakeCase,
        );
    }

    public function fill(
        array $data,
    ): self {
        foreach ($data as $key => $value) {
            $property = Str::camel($key);
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }

        return $this;
    }

    private function dto_to_array(
        BaseDTO $dto,
        bool $convertToSnakeCase = true,
    ): array {
        $vars = [];

        foreach (array_keys(get_class_vars($dto::class)) as $var_key) {
            $var = $dto->{$var_key};
            if ($var instanceof BaseDTO) {
                $var = $this->dto_to_array($var);
            } elseif ($var instanceof \Carbon\Carbon) {
                $var = $var->toDateTimeString();
            } elseif (is_object($var) && method_exists($var, 'toArray')) {
                $var = $var->toArray();
            }

            $vars[$convertToSnakeCase ? $this->camel_to_snake($var_key) : $var_key] = $var;
        }

        return $vars;
    }

    private function camel_to_snake(string $camel_case): string
    {
        if (str_starts_with($camel_case, 'ID')) {
            $camel_case = substr_replace(
                string: $camel_case,
                replace: 'id',
                offset: 0,
                length: 2,
            );

            if ($camel_case == 'id') {
                return $camel_case;
            }
        }
        $camel_case = str_replace(
            search: 'ID',
            replace: '_id',
            subject: $camel_case,
        );
        return strtolower(
            preg_replace(
                pattern: '/(?<=\\w)(?=[A-Z])|(?<=[a-z])(?=[0-9])/',
                replacement: '_',
                subject: $camel_case,
            )
        );
    }
}


