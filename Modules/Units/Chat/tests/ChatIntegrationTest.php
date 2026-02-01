<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:36 AM
 */

namespace Units\Chat\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Units\Chat\Common\Models\ChatMessageModel;
use Units\Chat\Common\Models\ChatThreadModel;
use Units\Chat\Common\Services\ChatService;
use Tests\TestCase;

/**
 * تست ادغام چت
 * Chat integration test
 *
 * تست ادغام واحد چت با مدل‌های دلخواه
 * Test for integrating chat unit with arbitrary models
 */
class ChatIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * تست ایجاد ترد چت برای مدل دلخواه
     * Test creating chat thread for arbitrary model
     *
     * @return void
     */
    public function test_can_create_chat_thread_for_arbitrary_model(): void
    {
        $chatService = app(ChatService::class);

        // Test with a hypothetical model (we'll use User model for testing)
        $chatService->actStartChat('App\Models\User', '1', [
            'title' => 'تست چت با مدل کاربر',
            'description' => 'توضیحات تست',
        ]);

        $this->assertFalse($chatService->hasErrors());

        $response = $chatService->getSuccessResponse();
        $data = $response->getData();

        $this->assertArrayHasKey('thread', $data);
        $this->assertInstanceOf(ChatThreadModel::class, $data['thread']);
        $this->assertEquals('App\Models\User', $data['thread']->model_type);
        $this->assertEquals(1, $data['thread']->model_id);
        $this->assertEquals('تست چت با مدل کاربر', $data['thread']->title);
    }

    /**
     * تست ارسال و دریافت پیام
     * Test sending and receiving messages
     *
     * @return void
     */
    public function test_can_send_and_receive_messages(): void
    {
        $chatService = app(ChatService::class);

        // Create a chat thread
        $chatService->actStartChat('App\Models\User', '1', [
            'title' => 'تست پیام',
        ]);

        $this->assertFalse($chatService->hasErrors());

        $thread = $chatService->getSuccessResponse()->getData()['thread'];

        // Send a message
        $chatService->actSendMessage($thread->id, 'applicant', 1, 'سلام، این یک پیام تست است');

        $this->assertFalse($chatService->hasErrors());

        $message = $chatService->getSuccessResponse()->getData()['message'];
        $this->assertInstanceOf(ChatMessageModel::class, $message);
        $this->assertEquals('سلام، این یک پیام تست است', $message->content);
        $this->assertEquals('applicant', $message->sender_type);
        $this->assertEquals(1, $message->sender_id);

        // Get messages
        $chatService->actGetThreadMessages($thread->id);
        $this->assertFalse($chatService->hasErrors());

        $messages = $chatService->getSuccessResponse()->getData()['messages'];
        $this->assertCount(1, $messages);
        $this->assertEquals('سلام، این یک پیام تست است', $messages->first()->content);
    }

    /**
     * تست چت بین دو کاربر
     * Test chat between two users
     *
     * @return void
     */
    public function test_chat_between_two_users(): void
    {
        $chatService = app(ChatService::class);

        // Create chat thread
        $chatService->actStartChat('App\Models\User', '1', [
            'title' => 'چت تست',
        ]);

        $this->assertFalse($chatService->hasErrors());
        $thread = $chatService->getSuccessResponse()->getData()['thread'];

        // First user sends message
        $chatService->actSendMessage($thread->id, 'applicant', 1, 'سلام از طرف درخواست‌کننده');
        $this->assertFalse($chatService->hasErrors());

        // Second user (agent) responds
        $chatService->actSendMessage($thread->id, 'agent', 2, 'سلام از طرف کارشناس');
        $this->assertFalse($chatService->hasErrors());

        // Get all messages
        $chatService->actGetThreadMessages($thread->id);
        $this->assertFalse($chatService->hasErrors());

        $messages = $chatService->getSuccessResponse()->getData()['messages'];
        $this->assertCount(2, $messages);

        // Check message order and content
        $messagesArray = $messages->toArray();
        $this->assertEquals('سلام از طرف درخواست‌کننده', $messagesArray[0]['content']);
        $this->assertEquals('applicant', $messagesArray[0]['sender_type']);
        $this->assertEquals('سلام از طرف کارشناس', $messagesArray[1]['content']);
        $this->assertEquals('agent', $messagesArray[1]['sender_type']);
    }

    /**
     * تست علامت‌گذاری پیام به عنوان دیده شده
     * Test marking message as seen
     *
     * @return void
     */
    public function test_mark_messages_as_seen(): void
    {
        $chatService = app(ChatService::class);

        // Create chat and send message
        $chatService->actStartChat('App\Models\User', '1', ['title' => 'چت تست']);
        $this->assertFalse($chatService->hasErrors());

        $thread = $chatService->getSuccessResponse()->getData()['thread'];
        $chatService->actSendMessage($thread->id, 'applicant', 1, 'پیام تست');
        $this->assertFalse($chatService->hasErrors());

        // Get message to check initial state
        $chatService->actGetThreadMessages($thread->id);
        $this->assertFalse($chatService->hasErrors());

        $messages = $chatService->getSuccessResponse()->getData()['messages'];
        $message = $messages->first();

        // Initially message should not be seen by other users
        $this->assertFalse($message->is_seen);

        // Mark as seen by different user
        $chatService->actMarkMessagesAsSeen($thread->id, 1); // except sender 1
        $this->assertFalse($chatService->hasErrors());

        // Refresh and check if marked as seen
        $chatService->actGetThreadMessages($thread->id);
        $messages = $chatService->getSuccessResponse()->getData()['messages'];
        $this->assertFalse($chatService->hasErrors());
    }

    /**
     * تست چت با مدل‌های مختلف
     * Test chat with different models
     *
     * @return void
     */
    public function test_chat_with_different_models(): void
    {
        $chatService = app(ChatService::class);

        // Test with different model types
        $models = [
            ['App\Models\User', '1'],
            ['App\Models\Post', '2'],
            ['App\Models\Order', '3'],
        ];

        foreach ($models as $model) {
            $chatService->actStartChat($model[0], $model[1], [
                'title' => "چت برای {$model[0]}-{$model[1]}",
            ]);

            $this->assertFalse($chatService->hasErrors());

            $thread = $chatService->getSuccessResponse()->getData()['thread'];
            $this->assertEquals($model[0], $thread->model_type);
            $this->assertEquals($model[1], $thread->model_id);
        }
    }
}
