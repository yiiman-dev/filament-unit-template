<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $config = app()->make('config');
        $config->set('filament-shield', require base_path('Modules/Units/Shield/My/config/filament-shield.php'));
        $config->set('permission', require base_path('Modules/Units/Shield/My/config/permission.php'));

        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';



        if (! $teams) {
            return;
        }
        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if (empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        if (! Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->hasColumn($tableNames['roles'], $columnNames['team_foreign_key'])) {
            Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->table($tableNames['roles'], function (Blueprint $table) use ($columnNames) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->after('id');
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');

                $table->dropUnique('roles_name_guard_name_unique');
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            });
        }

        if (! Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->hasColumn($tableNames['model_has_permissions'], $columnNames['team_foreign_key'])) {
            Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->table($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->default('1');
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                if (DB::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->getDriverName() !== 'sqlite') {
                    $table->dropForeign([$pivotPermission]);
                }
                $table->dropPrimary();

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
                if (DB::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->getDriverName() !== 'sqlite') {
                    $table->foreign($pivotPermission)
                        ->references('id')->on($tableNames['permissions'])->onDelete('cascade');
                }
            });
        }

        if (! Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->hasColumn($tableNames['model_has_roles'], $columnNames['team_foreign_key'])) {
            Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->table($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->default('1');
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                if (DB::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->getDriverName() !== 'sqlite') {
                    $table->dropForeign([$pivotRole]);
                }
                $table->dropPrimary();

                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
                if (DB::connection(\Modules\Basic\Helpers\Helper::migrationConnection('my'))->getDriverName() !== 'sqlite') {
                    $table->foreign($pivotRole)
                        ->references('id')->on($tableNames['roles'])->onDelete('cascade');
                }
            });
        }

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
