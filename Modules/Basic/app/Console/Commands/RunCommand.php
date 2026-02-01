<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Modules\Basic\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * RunCommand
 *
 * Runs three PHP built-in servers on ports 8000, 8100, and 8200 in the background.
 *
 * Error code: N/A
 * Singleton: false
 *
 * @see https://laravel.com/docs/10.x/valet
 */
class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run three PHP servers on ports 8000, 8100, and 8200 in the background';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $ports = [8000, 8100, 8200];
        $host = '0.0.0.0';
        foreach ($ports as $port) {
            $this->info("Starting PHP server on port {$port}...");
            $logFile = base_path("serve-{$port}.log");
            $cmd = "php artisan serve --host={$host} --port={$port} > {$logFile} 2>&1 &";
            $this->line("Executing: $cmd");
            exec($cmd);
            usleep(500000); // Give it a moment to start
        }
        // Test each port with curl
        $success = true;
        foreach ($ports as $port) {
            $url = "http://127.0.0.1:{$port}";
            $this->info("Testing server at {$url}...");
            $curlCmd = "curl -s -o /dev/null -w '%{http_code}' {$url}";
            $httpCode = shell_exec($curlCmd);
            if (trim($httpCode) === '200') {
                $this->info("SUCCESS: Server running at {$url}");
            } else {
                $this->error("FAILED: No response at {$url} (HTTP code: $httpCode)");
                $success = false;
            }
        }
        if ($success) {
            $this->info('All servers started and responded successfully.');
            return 0;
        } else {
            $this->error('One or more servers failed to start. Check serve-PORT.log files for details.');
            return 1;
        }
    }
} 