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

namespace Units\Chat\Common\Livewire;

use http\QueryString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Basic\BaseKit\Filament\HasNotification;
use Units\Auth\My\Models\UserModel;
use Units\Chat\Common\Enums\ChatPersonaEnum;
use Units\Chat\Common\Enums\ChatPersonaTermEnum;
use Units\Chat\Common\Models\ChatMessageModel;
use Units\Chat\Common\Services\ChatService;

/**
 * کامپوننت چت
 * Chat component
 *
 * کامپوننت لایوایر برای مدیریت ویژگی‌های زنده چت
 * Livewire component for managing chat real-time features
 */
class ChatComponent extends Component
{
    use HasNotification;

    public ?Model $record = null;

    public string $threadId = '';

    public array $messages = [];

    public string $newMessage = '';

    public string $senderId = '';

    public string $senderType = '';

    public bool $isTyping = false;

    public string $typingUser = '';

    public string $persona = '';

    public string $term = '';

    public string $tenant_national_code = '';

    public string $button_label='';

    protected $polling = true;

    protected $listeners = [];


    public function mount(
        ?Model $record = null,
        string $persona = '',
        string $term = '',
        $tenant_national_code = '',
        $button_label=''
    ): void {
        $this->button_label=$button_label;
        $this->record = $record;
        $this->persona = $persona;
        $this->term = $term;
        $this->tenant_national_code = $tenant_national_code;
        $this->senderId = $this->getSenderId();
        $this->senderType = $this->getSenderType();

        if ($this->record) {
            $this->loadThread();
            $this->loadMessages();
        }
    }

    /**
     * بارگذاری ترد چت
     * Load chat thread
     *
     * @return void
     * @throws \Exception
     */
    protected function loadThread(): void
    {
        $chatService = app(ChatService::class);
        if (!$this->checkRequirements()) {
            return;
        }
        $chatService->actStartChat(
            $this->persona,
            get_class($this->record),
            $this->record->getKey(), // Use getKey() to handle both ID and UUID
            [],
            $this->tenant_national_code
        );

        if (!$chatService->hasErrors()) {
            $thread = $chatService->getSuccessResponse()->getData()['thread'];
            $this->threadId = $thread->id;
        }
    }


    /**
     * بارگذاری پیام‌ها
     * Load messages
     *
     * @return void
     */
    protected function loadMessages(): void
    {
        if (!$this->threadId) {
            return;
        }

        $chatService = app(ChatService::class);
        $chatService->actGetThreadMessages($this->threadId);

        if (!$chatService->hasErrors()) {
            $messages = $chatService->getSuccessResponse()->getData()['messages'];
            $this->messages = $messages->toArray();

            // Mark messages as seen
            $this->markMessagesAsSeen();
        }
    }

    /**
     * بارگذاری دوره‌ای پیام‌ها
     * Poll messages periodically
     *
     * @return void
     */
    public function pollMessages(): void
    {
        if (!$this->threadId) {
            return;
        }

        $chatService = app(ChatService::class);
        $chatService->actGetThreadMessages($this->threadId);

        if (!$chatService->hasErrors()) {
            $newMessages = $chatService->getSuccessResponse()->getData()['messages']->toArray();

            if (count($newMessages) > count($this->messages)) {
                $this->messages = $newMessages;
                $this->markMessagesAsSeen();
            }
        }
    }

    /**
     * علامت‌گذاری پیام‌ها به عنوان دیده شده
     * Mark messages as seen
     *
     * @return void
     */
    protected function markMessagesAsSeen(): void
    {
        if (!$this->threadId) {
            return;
        }

        $chatService = app(ChatService::class);
        $chatService->actMarkMessagesAsSeen($this->threadId, $this->senderId);
    }

    protected function checkRequirements(): bool
    {
        if (empty($this->persona)) {
            throw new \Exception('Please config persona() for chatWidget class');
        }
        if ($this->persona != ChatPersonaEnum::COMMENT->value && empty($this->term)) {
            throw new \Exception('Please config term() for chatWidget class');
        }
        if ($this->persona == ChatPersonaEnum::CORPORATE_MANAGE->value and empty($this->tenant_national_code)) {
            throw new \Exception('Please set tenant_national_code() on chatWidget class');
        }
        return true;
    }

    /**
     * ارسال پیام جدید
     * Send new message
     *
     * @return void
     * @throws \Exception
     */
    public function sendMessage(): void
    {
        $this->loadThread();
        if (!$this->checkRequirements()) {
            return;
        }
        if (empty($this->getCurrentUserName())) {
            $this->alert_warning(
                'جهت ارسال پیام لطفا ابتدا از بخش پروفایل کاربری٬ نام و نام خانوادگی خود را تنظیم فرمایید.'
            );
            return;
        }
        // Clean and validate the TinyEditor HTML content
        $cleanMessage = trim(strip_tags($this->newMessage, '<p><br><strong><em><u><ol><ul><li>'));
        $cleanMessage = trim(str_replace(['<p>', '</p>', '<br>', '<br/>'], [' ', ' ', ' ', ' '], $cleanMessage));
        $cleanMessage = trim(str_replace(['&nbsp;', '&#160;'], ' ', $cleanMessage));

        if (empty($cleanMessage) || !$this->threadId) {
            $this->alert_warning('متنی برای ارسال وجود ندارد');
            return;
        }


        $chatService = app(ChatService::class);
        $chatService->actSendMessage(
            $this->threadId,
            $this->senderType,
            $this->senderId,
            $cleanMessage,
            [
                'username' => $this->getCurrentUserName()
            ]
        );

        if (!$chatService->hasErrors()) {
            $this->newMessage = '';
            $this->refreshMessages();
            $this->dispatch('chat-message-sent-' . $this->getId());
        }
    }


    private function getCurrentUserName()
    {
        return filament()->auth()->user()->getMeta('first_name') . ' ' . filament()->auth()->user()->getMeta(
                'last_name'
            );
    }


    /**
     * بازنشانی پیام‌ها
     * Refresh messages
     *
     * @return void
     */
    public
    function refreshMessages(): void
    {
        $this->loadMessages();
    }

    /**
     * شروع تایپ
     * Start typing
     *
     * @return void
     */
    public
    function startTyping(): void
    {
        $this->isTyping = true;
        switch ($this->persona) {
            case ChatPersonaEnum::CORPORATE_MANAGE->value:
                switch ($this->term) {
                    case ChatPersonaTermEnum::MANAGE_PANEL->value == $this->senderType:
                        $this->typingUser = '';
                        break;
                    case ChatPersonaTermEnum::MY_USER->value == $this->senderType:
                        $this->typingUser = 'متقاضی';
                }
        }
//        $this->typingUser = 'در حال نوشتن';

        // Stop typing after 2 seconds of inactivity
        $this->dispatch('typing-started');
    }

    /**
     * پایان تایپ
     * Stop typing
     *
     * @return void
     */
    public
    function stopTyping(): void
    {
        $this->isTyping = false;
        $this->dispatch('typing-stopped');
    }

    /**
     * دریافت آیدی فرستنده
     * Get sender ID
     *
     * @return int
     */
    protected
    function getSenderId(): int
    {
        $user = Auth::user();
        /**
         * @var UserModel|\Units\Auth\Admin\Models\UserModel|\Units\Auth\Manage\Models\UserModel $user
         */
        return $user ? $user->{$user->getKeyName()} : 0;
    }

    /**
     * تعیین نوع فرستنده
     * Determine sender type
     *
     * @return string
     */
    protected
    function getSenderType(): string
    {
        return $this->term;
    }

    /**
     * رندر کامپوننت
     * Render component
     *
     * @return \Illuminate\Contracts\View\View
     */
    public
    function render()
    {
        return view('common_chat::livewire.chat-component', [
            'messages' => collect($this->messages),
            'isTyping' => $this->isTyping,
            'typingUser' => $this->typingUser,
            'record' => $this->record
        ]);
    }

    public
    function senderTypeLabel(): string
    {
        return ';ksdjkl';
    }
}
