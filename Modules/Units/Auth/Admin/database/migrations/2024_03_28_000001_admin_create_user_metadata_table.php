<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('admin'))->create('user_metadata', function (Blueprint $table) {
            $table->string('phone_number')->primary();
            $table->string('profile_image')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('bio')->nullable();
            $table->text('address')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_logout_at')->nullable();
            $table->string('last_ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->string('deleted_reason', 255)->nullable();

            $table->foreign('phone_number')
                ->references('phone_number')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('admin'))->dropIfExists('user_metadata');
    }
};
