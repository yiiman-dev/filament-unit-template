<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection='laravel';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))
            ->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->timestamp('deleted_at')->nullable();
                $table->string('created_by', 255)->nullable();
                $table->string('updated_by', 255)->nullable();
                $table->string('deleted_by', 255)->nullable();
                $table->string('deleted_reason', 255)->nullable();
        });

        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))
            ->create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))
            ->create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))->dropIfExists('users');
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))->dropIfExists('password_reset_tokens');
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))->dropIfExists('sessions');
    }
};
