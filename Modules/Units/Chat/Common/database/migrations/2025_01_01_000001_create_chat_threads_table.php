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
     * ایجاد جدول چت تردها
     * Create chat threads table
     */
    public function up(): void
    {
        Schema::create('chat_threads', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('model_type')->comment('نوع مدل مرتبط - Model type (e.g., App\Models\FinanceRequest)');
            $table->string('model_id')->comment('آیدی مدل مرتبط - Related model ID');
            $table->json('meta')->nullable()->comment('اطلاعات متا - Meta information');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->string('tenant_id')->nullable();
            $table->string('persona')->comment('persona of chat panels');

            // ایجاد ایندکس‌ها - Create indexes
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
            $table->index('created_by');
        });
    }

    /**
     * حذف جدول چت تردها
     * Drop chat threads table
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_threads');
    }
};
