<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:42 PM
 */

namespace Modules\Basic\BaseKit;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Basic\Concerns\HasError;
use Modules\Basic\Concerns\InteractWithLog;

abstract class BaseService
{
    use HasError;
    use InteractWithLog;

    protected bool $shouldRollback = false;

    protected ?SuccessService $successResponse = null;

    protected bool $canSkipOnNull = false;


    /**
     * This method will refresh errors and response properties
     * @return void
     */
    public function refreshService(): self
    {
        $this->errors = [];
        $this->shouldRollback = false;
        $this->successResponse = null;
        $this->canSkipOnNull = false;
        return $this;
    }

    /**
     * Pass your content to handler function and return self class chain
     * @param $content
     * @return self
     */
    public static function send(...$args):self
    {

        return (new static())->handle(...$args);
    }


    public function __construct()
    {
        $this->refreshService();

        // Check for 'act' methods that return self or the actual class
        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'act') === 0 && preg_match('/[A-Z]/', $method)) {
                $reflectionMethod = new \ReflectionMethod($this, $method);

                $returnTypeHint = $reflectionMethod->getReturnType();
                if (empty($returnTypeHint)) {
                    throw new \InvalidArgumentException("Method {$method} in class " . get_class($this) . " must return the self.");
                }
                $returnTypeHint = $returnTypeHint->getName();

                // Allow both 'self' and the actual class name (they are equivalent in this context)
                $expectedClass = get_class($this);
                if ($returnTypeHint != 'self' && $returnTypeHint != $expectedClass) {
                    throw new \InvalidArgumentException("Method {$method} in class " . get_class($this) . " must return the self, but returns {$returnTypeHint}.");
                }
            }
        }
    }

    public function setCanSkipOnNull(): void
    {
        $this->canSkipOnNull = true;
    }

    public function canSkipOnNull(): bool
    {
        return $this->canSkipOnNull;
    }


    public function shouldRollback(): bool
    {
        return $this->shouldRollback;
    }

    public function setShouldRollback(bool $shouldRollback): void
    {
        $this->shouldRollback = $shouldRollback;
    }

    protected function setSuccessResponse(array $data = [], string $message = ''): void
    {
        $this->successResponse = new SuccessService($data, $message);
    }

    public function getSuccessResponse(): ?SuccessService
    {
        return $this->successResponse;
    }




}
