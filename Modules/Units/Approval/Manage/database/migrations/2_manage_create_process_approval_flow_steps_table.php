<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->create('process_approval_flow_steps', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_approval_flow_id')->constrained('process_approval_flows')->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->index();
            $table->json('permissions')->nullable();
            $table->string('panel');
            $table->integer('order')->nullable()->index();
            $table->string('action', 50)->default('APPROVE');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->dropIfExists('process_approval_flow_steps');
    }
};
