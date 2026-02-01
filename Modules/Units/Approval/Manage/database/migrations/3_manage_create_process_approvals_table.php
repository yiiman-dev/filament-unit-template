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
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->create('process_approvals', static function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type');
            $table->string('approvable_id');
            $table->foreignId('process_approval_flow_step_id')->nullable()->constrained('process_approval_flow_steps')->cascadeOnDelete();
            $table->string('approval_action', 12)->default('Approved');
            $table->text('approver_name')->nullable();
            $table->text('comment')->nullable();
            $table->string('user_id',14);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->dropIfExists('process_approvals');
    }
};
