<?php

namespace Modules\Basic\Concerns;

trait KnownUnitDB
{

    /**
     * بارگذاری منابع دیتابیس یونیت ها به صورت هوشمند
     */
    private function loadUnitDatabaseResources(): void
    {
        if ($this->app->runningInConsole()) {
            $basePath = app()->basePath('Modules/Units');

            // Migration ها
            $this->loadMigrationsFrom(
                $migrations=$this->findUnitDatabasePaths($basePath, 'migrations')
            );

            // Seeders ها
            $this->loadSeedersFrom(
                $this->findUnitDatabasePaths($basePath, 'seeders')
            );

            // Factories ها
            $this->loadFactories(
                $this->findUnitDatabasePaths($basePath, 'factories')
            );

        }
    }

    /**
     * یافتن مسیرهای دیتابیس در یونیت‌ها
     */
    protected function findUnitDatabasePaths(string $basePath, string $type): array
    {
        $paths = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir() ) {
                $path = $this->findDatabasePathInUnit($fileinfo->getPathname(), $type);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    /**
     * تشخیص پوشه‌های یونیت
     */
    protected function isUnitDirectory(\SplFileInfo $fileinfo): bool
    {
        return strpos($fileinfo->getFilename(), 'Unit') !== false;
    }

    /**
     * یافتن مسیر دیتابیس در یونیت
     */
    protected function findDatabasePathInUnit(string $unitPath, string $type): ?string
    {
        $possiblePaths = [
            $unitPath . "/database/{$type}",
            $unitPath . "/Database/" . ucfirst($type),
            $unitPath . "/db/{$type}",
            $unitPath . "/Database/{$type}",
            $unitPath . "/database/" . strtoupper($type)
        ];

        foreach ($possiblePaths as $path) {
            if (is_dir($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * بارگذاری Seeders ها
     */
    protected function loadSeedersFrom(array $paths): void
    {
        foreach ($paths as $path) {
            $this->app->afterResolving('seeder', function () use ($path) {
                $this->loadSeedsFrom($path);
            });
        }
    }

    /**
     * بارگذاری Factories ها
     */
    protected function loadFactories(array $paths): void
    {
        foreach ($paths as $path) {
            $this->app->singleton(\Illuminate\Database\Eloquent\Factory::class, function () use ($path) {
                return \Illuminate\Database\Eloquent\Factory::construct(
                    $this->app->make(\Faker\Generator::class),
                    $path
                );
            });
        }
    }


}
