<?php

namespace Units\StateCitiy\Common\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;


return new class extends Migration {


    public function up()
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->string('deleted_reason', 255)->nullable();
        });

        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->string('deleted_reason', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->dropIfExists('provinces');
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->dropIfExists('cities');
    }
};
