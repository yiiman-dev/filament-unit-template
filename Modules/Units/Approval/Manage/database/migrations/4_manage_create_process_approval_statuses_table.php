<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RingleSoft\LaravelProcessApproval\Enums\ApprovalStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->create('process_approval_statuses', static function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type');
            $table->string('approvable_id');
            $table->json('steps')->nullable();
            $table->string('status', 10)->default(ApprovalStatusEnum::CREATED->value);
            $table->foreignId('creator_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->dropIfExists('process_approval_statuses');
    }
};
