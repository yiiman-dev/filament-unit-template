<?php

namespace Units\ActLog\Manage\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Basic\Helpers\Helper;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection(Helper::migrationConnection('manage'))->create('act_logs', function (Blueprint $table) {
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
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->dropIfExists('act_logs');
    }
};
