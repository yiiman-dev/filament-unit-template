<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('admin'))->create('act_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('type');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('target_url')->nullable();
            $table->string('target_title')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
            $table->string('actor_number');
            $table->string('hash')->unique();
            $table->string('previous_hash')->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('admin'))->dropIfExists('act_logs');
    }
};
