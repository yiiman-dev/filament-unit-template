<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class FilamentModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament:list-modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all enabled Filament modules and export them to cache as JSON';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Load the list of enabled modules from config or another source
        $enabledModules = json_decode(file_get_contents(base_path('modules_statuses.json')));

        $filamentModules = [];
        $panels = [];

        // < Map modules as panel name >
        {
            foreach ($enabledModules as $pascalModuleName => $bool) {
                $snakedName = Str::snake($pascalModuleName);
                $arrayName = explode('_', $snakedName);
                $panelName = $arrayName[0];

                if (!isset($arrayName[1])) continue;


                $modulePath = base_path("Modules/{$pascalModuleName}");

                $filamentPath = "{$modulePath}/app/Resources/Filament";

                if (File::exists($filamentPath) && File::isDirectory($filamentPath)) {

                    $phpFiles = File::files($filamentPath);

                    // If the directory contains PHP files, add it to the array
                    if (!empty($phpFiles)) {
                        // Assuming namespace follows a pattern based on module name
                        $namespace = "App\Modules\\{$pascalModuleName}\Resources\Filament";

                        $panels[$panelName]["Modules/{$pascalModuleName}/app/Resources/Filament"] = $namespace;
                        $this->info("Added {$pascalModuleName} to Filament modules list.");
                    }
                }


            }
        }
        // </ Map modules as panel name >


        // Export the array as a JSON file in Laravel's cache directory
        $cacheFilePath = storage_path('framework/cache/filament_modules.json');
        File::put($cacheFilePath, json_encode($panels, JSON_PRETTY_PRINT));

        $this->info("Filament modules list exported to {$cacheFilePath}");

        return 0;
    }
}
