<?php

namespace Units\ActLog\Admin\Providers;

use Closure;
use Illuminate\Support\ServiceProvider;
use Units\ActLog\Admin\Console\VerifyLogChainCommand;

class AdminActLogProvider extends ServiceProvider
{
    public function boot():void
    {
        $this->registerCommands();
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            VerifyLogChainCommand::class
        ]);
    }
}
