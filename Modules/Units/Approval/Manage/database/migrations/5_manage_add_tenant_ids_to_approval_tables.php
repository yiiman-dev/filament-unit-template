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
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->table('process_approval_flow_steps', static function (Blueprint $table) {
            $table->string('tenant_id', 38)->index()->nullable()->after('active');
        });
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->table('process_approvals', static function (Blueprint $table) {
            $table->string('tenant_id', 38)->index()->nullable()->after('user_id');
        });
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->table('process_approval_statuses', static function (Blueprint $table) {
            $table->string('tenant_id', 38)->index()->nullable()->after('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->hasColumn('process_approval_flow_steps', 'tenant_id')) {
            Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->table('process_approval_flow_steps', static function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
        if(Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->hasColumn('process_approvals', 'tenant_id')) {
            Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->table('process_approvals', static function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
        if(Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->hasColumn('process_approval_statuses', 'tenant_id')) {
            Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->table('process_approval_statuses', static function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};
