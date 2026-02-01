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
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->create('process_approval_flows', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('approvable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->dropIfExists('process_approval_flows');
    }
};
