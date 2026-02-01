<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    private function check_database_schema($connection)
    {
        // Get all user-defined tables in public schema
        $tables = DB::connection('test_'.$connection)
            ->table('information_schema.tables')
            ->where('table_schema', $connection)
            ->where('table_type', 'BASE TABLE')
            ->pluck('table_name');
        foreach ($tables as $tableName) {
            // Skip system or special tables
            if (in_array(
                $tableName,
                [
                    'migrations',
                    'failed_jobs',
                    'password_reset_tokens',
                    'roles',
                    'model_has_roles',
                    'sessions',
                    'cache',
                    'cache_locks',
                    'jobs',
                    'job_batches',
                    'model_has_permissions',
                    'notifications',
                    'exports',
                    'imports',
                    'failed_import_rows',
                    'permissions',
                    'act_logs',
                    'role_has_permissions',
                ]
            )) {
                continue;
            }

            $columns = DB::table('information_schema.columns')
                ->where('table_schema', $connection)
                ->where('table_name', $tableName)
                ->pluck('column_name')
                ->toArray();

            $this->assertContains('created_at', $columns, "Table '{$tableName}' is missing 'created_at' on connection '".$connection."'");
            $this->assertContains('updated_at', $columns, "Table '{$tableName}' is missing 'updated_at' on connection '".$connection."'");
            $this->assertContains('deleted_at', $columns, "Table '{$tableName}' is missing 'deleted_at' (soft deletes) on connection '".$connection."'");
            $this->assertContains('created_by', $columns, "Table '{$tableName}' is missing 'created_by' on connection '".$connection."'");
            $this->assertContains('updated_by', $columns, "Table '{$tableName}' is missing 'updated_by' on connection '".$connection."'");
            $this->assertContains('deleted_by', $columns, "Table '{$tableName}' is missing 'deleted_by' on connection '".$connection."'");
            $this->assertContains('deleted_reason', $columns, "Table '{$tableName}' is missing 'deleted_reason' on connection '".$connection."'");
        }
    }

    /** @test */
    public function all_tables_have_timestamps_and_soft_deletes()
    {
        $this->check_database_schema('my');
        $this->check_database_schema('manage');
        $this->check_database_schema('admin');
    }
}
