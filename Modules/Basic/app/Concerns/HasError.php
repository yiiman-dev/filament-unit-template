<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/8/25, 2:31 AM
 */

namespace Modules\Basic\Concerns;

use Illuminate\Support\Collection;
use Modules\Basic\BaseKit\ErrorService;

trait HasError
{

    protected array $errors = [];

    /**
     *
     * @return ErrorService[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    public function getErrorCollection():Collection
    {
        return Collection::make($this->errors);
    }

    public function getErrorMessages(): array
    {
        return array_map(fn($error) => $error->getMessage(), $this->errors);
    }

    public function addError(array|string $data = [], string $message = ''): void
    {
        $this->errors[] = new ErrorService($data, $message);
        $info = getReferrerInfo();
        \Log::error(is_string($data)?$data:$message, [
            'class' => self::class,
            'line' => $info['line'],
            'file' => $info['file'],
        ]);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return bool will return true if service has not any error
     */
    public function hasNotError(): bool
    {
        return !$this->hasErrors();
    }

    public function handleModelErrors($model): void
    {
        if ($errors = $model->getErrors()) {
            foreach ($errors as $field => $messages) {
                $this->addError(['field' => $field], implode(', ', $messages));
            }
        }
    }
}
