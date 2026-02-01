<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{




    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('admin'))->create('users', function (Blueprint $table) {

            $table->string('username')->unique();
            $table->string('phone_number', 14)->unique()->primary();
            $table->tinyInteger('status')->notNull();
            $table->string('password_hash');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->string('deleted_reason', 255)->nullable();
            $table->string('deactivated_reason', 255)->nullable();

            // Add unique index for (national_code, phone_number, deleted_at)
            $table->unique(['username', 'phone_number', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('admin'))->dropIfExists('users');
    }
};
