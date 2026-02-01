<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:28 AM
 */

namespace Units\Chat\Common\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Basic\BaseKit\BaseService;
use Units\Auth\Manage\Models\UserModel;
use Units\Chat\Common\Enums\ChatPersonaEnum;
use Units\Chat\Common\Models\ChatMessageModel;
use Units\Chat\Common\Models\ChatThreadModel;
use Units\Chat\Common\Repository\ChatRepo;

/**
 * سرویس چت
 * Chat service
 *
 * مسئول مدیریت منطق کسب و کار چت
 * Responsible for managing chat business logic
 */
class ChatService extends BaseService
{
    /**
     * @var ChatRepo
     */
    private ChatRepo $_chatRepo;

    /**
     * سازنده
     * Constructor
     *
     * @param ChatRepo $chatRepo
     */
    public function __construct(ChatRepo $chatRepo)
    {
        if (is_string($chatRepo)){
            $this->_chatRepo = new ChatRepo();
        }else{
            $this->_chatRepo = $chatRepo;
        }
    }

    /**
     * شروع یک چت جدید برای یک مدل
     * Start a new chat for a model
     *
     * return data:
     * ```
     *  [
     *      'thread' => ChatThreadModel,
     *      'created' => bool
     *  ]
     * ```
     *
     * @param string $modelType نوع مدل - Model type
     * @param string $modelId آیدی مدل - Model ID (supports both integer and UUID)
     * @param array $attributes ویژگی‌های ترد - Thread attributes
     * @return self
     */
    public function actStartChat(string $persona,string $modelType, string $modelId, array $attributes = [],$tenant_national_code=''): self
    {
        DB::beginTransaction();
        try {
            $thread = $this->_chatRepo->findOrCreateThread($persona,$modelType, $modelId, $attributes,$tenant_national_code);
            $created = !$thread->wasRecentlyCreated;

            DB::commit();
            $this->setSuccessResponse([
                'thread' => $thread,
                'created' => $created,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError([], $e->getMessage());
        }
        return $this;
    }



    /**
     * ارسال یک پیام جدید
     * Send a new message
     *
     * return data:
     * ```
     *  [
     *      'message' => ChatMessageModel
     *  ]
     * ```
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @param string $senderType نوع فرستنده - Sender type (applicant/agent)
     * @param int $senderId آیدی فرستنده - Sender ID
     * @param string $content محتوای پیام - Message content
     * @param array $meta متا دیتا - Meta data
     * @return self
     */
    public function actSendMessage(string $threadId, string $senderType, int $senderId, string $content, array $meta = []): self
    {
        DB::beginTransaction();
        try {
            $message = $this->_chatRepo->createMessage($threadId, $senderType, $senderId, $content, $meta);

            DB::commit();
            $this->setSuccessResponse([
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError([], $e->getMessage());
        }
        return $this;
    }

    /**
     * دریافت تمام پیام‌های یک ترد
     * Get all messages for a thread
     *
     * return data:
     * ```
     *  [
     *      'messages' => Collection
     *  ]
     * ```
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @return self
     */
    public function actGetThreadMessages(string $threadId): self
    {
        try {
            $messages = $this->_chatRepo->getMessagesByThread($threadId)
            ->each(function (ChatMessageModel $chat_message_model) {
                $chat_message_model->sender_type=$this->sender_label($chat_message_model);
                return $chat_message_model;
            });

            $this->setSuccessResponse([
                'messages' => $messages,
            ]);
        } catch (\Exception $e) {
            $this->addError([], $e->getMessage());
        }
        return $this;
    }


    public function sender_label(ChatMessageModel $chat_message_model):string
    {
        switch ($chat_message_model->chatThread->persona){
            case ChatPersonaEnum::CORPORATE_MANAGE->value:
            case ChatPersonaEnum::COMMENT->value:
                $creator=str($chat_message_model->created_by)->explode('_')->toArray();
                switch ($creator[0]){
                    case 'manage':
                        switch (filament()->getCurrentPanel()->getId()){
                            case 'manage':
                                return $chat_message_model->meta['username'];
                            case 'my':
                                return 'کارگزار';
                        }
                    case 'my':
                        return $chat_message_model->meta['username'];
                }

            case ChatPersonaEnum::CORPORATE_CORPORATE->value:
            case ChatPersonaEnum::MANAGE_ADMIN->value:
            case ChatPersonaEnum::MANAGE_MANAGE->value:
            case ChatPersonaEnum::MYUSER_MANAGE:
            default:
                return  '';
        }
    }

    /**
     * دریافت تردهای چت برای یک مدل
     * Get chat threads for a model
     *
     * return data:
     * ```
     *  [
     *      'threads' => Collection
     *  ]
     * ```
     *
     * @param string $modelType نوع مدل - Model type
     * @param string $modelId آیدی مدل - Model ID (supports both integer and UUID)
     * @return self
     */
    public function actGetModelThreads(string $modelType, string $modelId): self
    {
        try {
            $thread = $this->_chatRepo->getThreadByModel($modelType, $modelId);
            $threads = $thread ? collect([$thread]) : collect();

            $this->setSuccessResponse([
                'threads' => $threads,
            ]);
        } catch (\Exception $e) {
            $this->addError([], $e->getMessage());
        }
        return $this;
    }

    /**
     * دریافت تمام تردهای چت برای یک نوع مدل
     * Get all chat threads for a model type
     *
     * return data:
     * ```
     *  [
     *      'threads' => Collection
     *  ]
     * ```
     *
     * @param string $modelType نوع مدل - Model type
     * @return self
     */
    public function actGetThreadsByModelType(string $modelType): self
    {
        try {
            $threads = $this->_chatRepo->getThreadsByModelType($modelType);

            $this->setSuccessResponse([
                'threads' => $threads,
            ]);
        } catch (\Exception $e) {
            $this->addError([], $e->getMessage());
        }
        return $this;
    }

    /**
     * علامت‌گذاری پیام‌ها به عنوان دیده شده
     * Mark messages as seen
     *
     * return data:
     * ```
     *  [
     *      'updated_count' => int
     *  ]
     * ```
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @param int $exceptSenderId آیدی فرستنده استثنا - Exception sender ID
     * @return self
     */
    public function actMarkMessagesAsSeen(string $threadId, string $exceptSenderId): self
    {
        try {
            $updatedCount = $this->_chatRepo->markThreadMessagesAsSeen($threadId, $exceptSenderId);

            $this->setSuccessResponse([
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            $this->addError([], $e->getMessage());
        }
        return $this;
    }

    /**
     * دریافت تعداد پیام‌های خوانده نشده
     * Get unread message count
     *
     * return data:
     * ```
     *  [
     *      'unread_count' => int
     *  ]
     * ```
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @param int $exceptSenderId آیدی فرستنده استثنا - Exception sender ID
     * @return self
     */
    public function actGetUnreadCount(string $threadId, string $exceptSenderId): self
    {
        try {
            $unreadCount = $this->_chatRepo->getUnreadCount($threadId, $exceptSenderId);

            $this->setSuccessResponse([
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            $this->addError([], $e->getMessage());
        }
        return $this;
    }
}
