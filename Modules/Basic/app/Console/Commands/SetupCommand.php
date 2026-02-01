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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

/**
 * SetupCommand
 *
 * Runs initial setup for the application including Docker, key generation, migrations, and dev tasks.
 *
 * Error code: N/A
 * Singleton: false
 *
 * @see https://laravel.com/docs/10.x/artisan
 */
class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run initial setup: docker, key, modules, migrate, dev';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting setup...');

        // 1. Docker Compose Up
        $this->info('Running: docker compose up -d');
        $process = Process::fromShellCommandline('docker compose up -d');
        $process->setTimeout(300);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });
        if (!$process->isSuccessful()) {
            $this->error('Docker compose failed: ' . $process->getErrorOutput());
            return 1;
        }
        $this->output->write('Waiting for docker postgresql to start...');
        sleep(10);
        // 2. Generate app key if not present
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';
        if (!preg_match('/^APP_KEY=\w+/m', $envContent)) {
            $this->info('No APP_KEY found. Generating application key...');
            Artisan::call('key:generate');
            $this->line(Artisan::output());
        } else {
            $this->info('APP_KEY already exists.');
        }

        // 3. List Filament Modules
        $this->info('Listing Filament modules...');
        Artisan::call('filament:list-modules');
        $this->line(Artisan::output());

        // 4. Migrate Fresh
        $this->info('Running migrations (fresh)...');
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->line(Artisan::output());

        // 5. Run dev command
        $this->info('Running dev tasks...');
        Artisan::call('dev:onboarding');
        $this->line(Artisan::output());

        $this->info('Setup- completed successfully.');
        return 0;
    }
}
