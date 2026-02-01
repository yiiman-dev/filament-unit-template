<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:26 AM
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ایجاد جدول پیام‌های چت
     * Create chat messages table
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('chat_thread_id')->comment('آیدی ترد چت - Chat thread ID');
            $table->string('sender_type')->comment('نوع فرستنده - Sender type (applicant/agent)');
            $table->string('sender_id')->comment('آیدی فرستنده - Sender ID');
            $table->text('content')->comment('محتوای پیام - Message content');
            $table->boolean('is_seen')->default(false)->comment('نشانه دیده شدن - Seen indicator');
            $table->timestamp('seen_at')->nullable()->comment('زمان دیده شدن - Time when seen');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->json('meta')->nullable()->comment('اطلاعات متا - Meta information');

            // ایجاد ایندکس‌ها - Create indexes
            $table->index('chat_thread_id');
            $table->index(['sender_type', 'sender_id']);
            $table->index('is_seen');

            // ایجاد فورین کی - Create foreign key
            $table->foreign('chat_thread_id')->references('id')->on('chat_threads')->onDelete('cascade');
        });
    }

    /**
     * حذف جدول پیام‌های چت
     * Drop chat messages table
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
