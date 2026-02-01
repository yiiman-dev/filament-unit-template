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
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))->create('failed_import_rows', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->foreignId('import_id')->constrained()->cascadeOnDelete();
            $table->text('validation_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('laravel'))->dropIfExists('failed_import_rows');
    }
};
