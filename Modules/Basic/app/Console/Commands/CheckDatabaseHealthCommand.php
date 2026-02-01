<?php

namespace Modules\Basic\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class CheckDatabaseHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check database connections health';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $databaseConnections = collect(config('database.connections'));

        $this->checkMongoConnectionsHealth($databaseConnections);

        $this->checkPostgresConnectionsHealth($databaseConnections);

        $this->checkSessionHealth();

        $this->checkCacheHealth();

        $this->checkQueueHealth();
    }

    private function checkMongoConnectionsHealth($databaseConnections): void
    {
        $mongoConnections = $databaseConnections
            ->filter(fn(array $dbConnection) => $dbConnection['driver'] == 'mongodb');

        $mongoConnections->each(function (array $connectionDetails, string $connectionName) {
            try {
                DB::connection($connectionName)->getMongoClient()->listDatabases();
                $this->info("✅ {$connectionName} is healthy and connected!");
            } catch (\Exception $e) {
                $this->error("❌ {$connectionName} Connection Failed: {$e->getMessage()}");
            }
        });
    }

    private function checkPostgresConnectionsHealth($databaseConnections): void
    {
        $mongoConnections = $databaseConnections
            ->filter(fn(array $dbConnection) => $dbConnection['driver'] == 'pgsql');

        $mongoConnections->each(function (array $connectionDetails, string $connectionName) {
            try {
                DB::connection($connectionName)->getPdo();
                $this->info("✅ {$connectionName} is healthy and connected!");
            } catch (\Exception $e) {
                $this->error("❌ {$connectionName} Connection Failed: {$e->getMessage()}");
            }
        });
    }

    private function checkSessionHealth(): void
    {
        try {
            // Test session write
            Session::put('health_check', 'session_test');

            // Test session read
            $value = Session::get('health_check');

            if ($value === 'session_test') {
                $this->info("✅ Session is healthy and connected!");
            } else {
                $this->error("❌ Session Read Failed: Could not read written session value");
            }
        } catch (\Exception $e) {
            $this->error("❌ Session Connection Failed: {$e->getMessage()}");
        }
    }

    private function checkCacheHealth(): void
    {
        try {
            // Test cache write
            Cache::put('health_check', 'cache_test', 60);

            // Test cache read
            $value = Cache::get('health_check');

            if ($value === 'cache_test') {
                $this->info("✅ Cache is healthy and connected!");
            } else {
                $this->error("❌ Cache Read Failed: Could not read written cache value");
            }
        } catch (\Exception $e) {
            $this->error("❌ Cache Connection Failed: {$e->getMessage()}");
        }
    }

    private function checkQueueHealth(): void
    {
        try {
            // Test queue by checking if we can access the queue manager
            Queue::connection();
            $this->info("✅ Queue is healthy and connected!");
        } catch (\Exception $e) {
            $this->error("❌ Queue Connection Failed: {$e->getMessage()}");
        }
    }
}
