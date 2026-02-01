<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:46 AM
 */

namespace Units\Chat\Common\Filament\Widgets;

use Filament\Forms\Components\Field;
use Filament\Forms\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Units\Chat\Common\Enums\ChatPersonaEnum;
use Units\Chat\Common\Enums\ChatPersonaTermEnum;

/**
 * ویجت فرم چت
 * Chat form widget
 *
 * کامپوننت فرم برای ادغام چت در فرم‌های فیلمنت
 * Form component for integrating chat in Filament forms
 */
class ChatFormWidget extends Field
{
    protected string $view = 'common_chat::filament.widgets.chat-form';//dont change this

    protected array $columnSpan = ['default' => 'full'];

    public mixed $record = null;

    protected string $modelType = '';

    protected string $modelId = '';


    protected string $persona = '';

    protected string $term = '';

    protected string $tenant_national_id = '';

    protected string $button_label = 'ارسال';

    /**
     * تنظیم مدل برای چت
     * Set model for chat
     *
     * @param Model|\Closure|null $record
     * @return self
     */
    public function record(Model|\Closure|null $record = null): self
    {
        $this->record = $record;

        if ($record instanceof Model) {
            $this->modelType = get_class($record);
            $this->modelId = $record->getKey(); // Use getKey() to handle both ID and UUID
        } elseif ($record instanceof \Closure) {
            // We'll resolve the closure later in getComponentData
            $this->modelType = '';
            $this->modelId = '';
        } else {
            $this->modelType = '';
            $this->modelId = '';
        }

        return $this;
    }

    public function buttonLabel($button_label): self
    {
        $this->button_label = $button_label;
        return $this;
    }

    public function getButtonLabel()
    {
        return $this->button_label;
    }

    public function term(ChatPersonaTermEnum $term)
    {
        $this->term = $term->value;
        return $this;
    }

    public function persona(ChatPersonaEnum $persona)
    {
        $this->persona = $persona->value;
        return $this;
    }

    public function tenantNationalCode(string $tenant_national_code)
    {
        $this->tenant_national_id = $tenant_national_code;
        return $this;
    }

    public function getPersona()
    {
        return $this->persona;
    }

    public function getTenantNationalCode()
    {
        return $this->tenant_national_id;
    }

    /**
     * ایجاد نمونه جدید
     * Create new instance
     *
     * @param string $name
     * @return static
     */
    public static function make(string $name): static
    {
        $field = app(static::class, ['name' => $name]);
        $field->configure();
        return $field;
    }

    /**
     * پیکربندی کامپوننت
     * Configure component
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
        $this->disabled();
    }

    /**
     * ایجاد داده‌های کامپوننت
     * Create component data
     *
     * @param Context $context
     * @return array
     */
    public function getComponentData($context): array
    {
        if (empty($this->persona)) {
            throw new \Exception('Please config persona() for chatWidget class');
        }
        if ($this->persona != ChatPersonaEnum::COMMENT->value && empty($this->term)) {
            throw new \Exception('Please config term() for chatWidget class');
        }
        // Ensure the record is properly resolved (not a closure)
        $resolvedRecord = $this->record;

        // If record is a closure, evaluate it
        if ($resolvedRecord instanceof \Closure) {
            $resolvedRecord = $resolvedRecord();
        }

        // Only pass the record if it's a proper Model instance
        if ($resolvedRecord instanceof Model) {
            $modelType = get_class($resolvedRecord);
            $modelId = $resolvedRecord->getKey();
        } else {
            $modelType = '';
            $modelId = '';
            $resolvedRecord = null;
        }

        return [
            'record' => $resolvedRecord,
            'modelType' => $modelType,
            'modelId' => $modelId,
            'persona' => $this->persona,
            'term' => $this->term,
            'button_label' => $this->button_label,
            'tenant_national_code' => $this->tenant_national_id,
            'senderType' => $this->getSenderType(),
            'senderId' => $this->getSenderId(),
        ];
    }

    /**
     * تعیین نوع فرستنده
     * Determine sender type
     *
     * @return string
     */
    public function getSenderType(): string
    {
        return $this->term;
    }

    /**
     * دریافت آیدی فرستنده
     * Get sender ID
     *
     * @return int
     */
    protected function getSenderId(): int
    {
        $user = Auth::user();
        return $user ? $user->id : 0;
    }
}
