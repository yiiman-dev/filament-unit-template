<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:56 AM
 */

namespace Units\Chat\Common\Filament\Widgets;

use Filament\Forms\Components\Field;
use Filament\Forms\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Units\Chat\Common\Models\ChatThreadModel;
use Units\Chat\Common\Services\ChatService;

/**
 * ویجت اینفولیست چت
 * Chat infolist widget
 *
 * کامپوننت فرم برای نمایش پیام‌های چت در اینفولیست
 * Form component for displaying chat messages in infolist
 */
class ChatInfolistWidget extends Field
{
    protected string $view = 'chat::filament.widgets.chat-infolist';

    protected int | string | array $columnSpan = 'full';

    protected ?Model $record = null;

    protected string $modelType = '';

    protected string $modelId = '';

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->disabled();
    }

    /**
     * تنظیم مدل برای چت
     * Set model for chat
     *
     * @param Model $record
     * @return self
     */
    public function record(Model $record): self
    {
        $this->record = $record;
        $this->modelType = get_class($record);
        $this->modelId = $record->getKey(); // Use getKey() to handle both ID and UUID
        return $this;
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
    }

    /**
     * ایجاد داده‌های کامپوننت
     * Create component data
     *
     * @param Context $context
     * @return array
     */
    public function getComponentData(Context $context): array
    {
        $messages = [];
        $senderId = $this->getSenderId();

        if ($this->record) {
            $thread = $this->getThread();
            if ($thread) {
                $chatService = app(ChatService::class);
                $chatService->actGetThreadMessages($thread->id);

                if (!$chatService->hasErrors()) {
                    $messages = $chatService->getSuccessResponse()->getData()['messages']->toArray();

                    // Mark messages as seen
                    $chatService->actMarkMessagesAsSeen($thread->id, $senderId);
                }
            }
        }

        return [
            'record' => $this->record,
            'messages' => $messages,
            'senderId' => $senderId,
            'senderType' => $this->getSenderType(),
        ];
    }

    /**
     * دریافت ترد چت
     * Get chat thread
     *
     * @return ChatThreadModel|null
     */
    protected function getThread(): ?ChatThreadModel
    {
        if (!$this->record) {
            return null;
        }

        $chatService = app(ChatService::class);
        $chatService->actGetModelThreads($this->modelType, $this->modelId);

        if ($chatService->hasErrors()) {
            return null;
        }

        $threads = $chatService->getSuccessResponse()->getData()['threads'];
        return $threads->first();
    }

    /**
     * تعیین نوع فرستنده
     * Determine sender type
     *
     * @return string
     */
    protected function getSenderType(): string
    {
        $panel = request()->route()->parameter('panel');
        return $panel === 'manage' ? 'agent' : 'applicant';
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
