<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:27 AM
 */

namespace Units\Chat\Common\Repository;

use http\Encoding\Stream\Inflate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Units\Chat\Common\Models\ChatMessageModel;
use Units\Chat\Common\Models\ChatThreadModel;

/**
 * مخزن چت
 * Chat repository
 *
 * مسئول مدیریت عملیات پایگاه داده برای چت
 * Responsible for managing database operations for chat
 */
class ChatRepo
{
    public static function make(): self
    {
        return new static();
    }
    /**
     * یافتن یا ایجاد ترد چت برای یک مدل خاص
     * Find or create chat thread for a specific model
     *
     * @param string $modelType نوع مدل - Model type
     * @param string $modelId آیدی مدل - Model ID (supports both integer and UUID)
     * @param array $attributes ویژگی‌های اختیاری - Optional attributes
     * @return ChatThreadModel
     */
    public function findOrCreateThread(
        string $persona,
        string $modelType,
        string $modelId,
        array $attributes = [],
        $tenant_national_code = ''
    ): ChatThreadModel {
        return ChatThreadModel::firstOrCreate([
            'persona' => $persona,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'tenant_id' => $tenant_national_code
        ], $attributes);
    }

    /**
     * دریافت تردهای چت برای یک مدل خاص
     * Get chat threads for a specific model type
     *
     * @param string $modelType نوع مدل - Model type
     * @param string $modelId آیدی مدل - Model ID (supports both integer and UUID)
     * @return ChatThreadModel|null
     */
    public function getThreadByModel(string $modelType, string $modelId): ?ChatThreadModel
    {
        return ChatThreadModel::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->first();
    }

    /**
     * دریافت تمام تردهای چت برای یک نوع مدل
     * Get all chat threads for a model type
     *
     * @param string $modelType نوع مدل - Model type
     * @return Collection
     */
    public function getThreadsByModelType(string $modelType): Collection
    {
        return ChatThreadModel::where('model_type', $modelType)
            ->with([
                'messages' => function ($query) {
                    $query->latest()->limit(10);
                }
            ])
            ->latest()
            ->get();
    }

    /**
     * دریافت تمام پیام‌های یک ترد چت
     * Get all messages for a chat thread
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @return Collection
     */
    public function getMessagesByThread(string $threadId): Collection
    {
        return ChatMessageModel::where('chat_thread_id', $threadId)
            ->with('chatThread')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * ایجاد یک پیام جدید
     * Create a new message
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @param string $senderType نوع فرستنده - Sender type (applicant/agent)
     * @param int $senderId آیدی فرستنده - Sender ID
     * @param string $content محتوا - Content
     * @param array $meta متا دیتا - Meta data
     * @return ChatMessageModel
     */
    public function createMessage(
        string $threadId,
        string $senderType,
        int $senderId,
        string $content,
        array $meta = []
    ): ChatMessageModel {
        return ChatMessageModel::create([
            'chat_thread_id' => $threadId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'content' => $content,
            'meta' => $meta,
        ]);
    }

    /**
     * علامت‌گذاری تمام پیام‌ها به عنوان دیده شده برای یک ترد
     * Mark all messages as seen for a thread
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @param int $exceptSenderId آیدی فرستنده استثنا - Exception sender ID
     * @return int تعداد پیام‌های به‌روزرسانی شده - Number of updated messages
     */
    public function markThreadMessagesAsSeen(string $threadId, string $exceptSenderId): int
    {
        return ChatMessageModel::where('chat_thread_id', $threadId)
            ->where('sender_id', '!=', $exceptSenderId)
            ->where('is_seen', false)
            ->update([
                'is_seen' => true,
                'seen_at' => now(),
            ]);
    }

    /**
     * دریافت آخرین پیام‌های تردها
     * Get latest messages from threads
     *
     * @param array $threadIds آیدی تردها - Thread IDs
     * @return Collection
     */
    public function getLatestMessages(array $threadIds): Collection
    {
        return ChatMessageModel::whereIn('chat_thread_id', $threadIds)
            ->whereIn('id', function ($query) use ($threadIds) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('chat_messages')
                    ->whereIn('chat_thread_id', $threadIds)
                    ->groupBy('chat_thread_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * دریافت تعداد پیام‌های خوانده نشده
     * Get unread message count
     *
     * @param int $threadId آیدی ترد - Thread ID
     * @param int $exceptSenderId آیدی فرستنده استثنا - Exception sender ID
     * @return int
     */
    public function getUnreadCount(string $threadId, string $exceptSenderId): int
    {
        return ChatMessageModel::where('chat_thread_id', $threadId)
            ->where('sender_id', '!=', $exceptSenderId)
            ->where('is_seen', false)
            ->count();
    }
}
