<?php

namespace Units\Auth\My\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Basic\Helpers\Helper;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->connection = Helper::migrationConnection('my');
        Schema::connection(Helper::migrationConnection('my'))->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('national_code', 10);
            $table->string('phone_number', 14);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('validate_status')->default(0)->comment('check phone number and national code is for validated person');
            $table->timestamp('validate_request_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->string('deleted_reason', 255)->nullable();

            $table->unique(['national_code', 'phone_number', 'deleted_at']);
        });

        Schema::connection(Helper::migrationConnection('my'))->create('user_meta', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('national_code', 10);
            $table->string('meta_key');
            $table->string('meta_value');
            $table->string('meta_extra')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->string('deleted_reason', 255)->nullable();
            // Ensure logical uniqueness even with soft deletes
            $table->unique(['national_code', 'meta_key', 'deleted_at']);
            $table->index('meta_key');
        });

        Schema::connection(Helper::migrationConnection('my'))->create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->connection = $this->getConnection();

        Schema::connection(Helper::migrationConnection('my'))->dropIfExists('sessions');
        Schema::connection(Helper::migrationConnection('my'))->dropIfExists('password_reset_tokens');
        Schema::connection(Helper::migrationConnection('my'))->dropIfExists('user_meta');
        Schema::connection(Helper::migrationConnection('my'))->dropIfExists('users');
    }

    /**
     * @return mixed|string
     */
    public function getConnection(): mixed
    {
        return Helper::migrationConnection('my');
    }
};
