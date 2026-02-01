<?php

namespace Units\Approval\Manage\Services;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use File;

class ModelScannerService
{
    public function getApprovableModels(): array
    {
        $models = [];

        // Find all Manage/Models directories recursively in Units
        $manageModelsDirectories = $this->findManageModelsDirectories();

        foreach ($manageModelsDirectories as $directory) {
            if (File::exists($directory)) {
                $models = array_merge($models, $this->scanDirectoryForApprovableModels($directory));
            }
        }

        return $models;
    }

    /**
     * Find all Manage/Models directories recursively in Modules/Units
     */
    private function findManageModelsDirectories(): array
    {
        $unitsPath = base_path('Modules/Units');
        $directories = [];

        if (!File::exists($unitsPath)) {
            return $directories;
        }

        // Search for pattern: Modules/Units/*/Manage/Models/*
        $level1Units = File::directories($unitsPath);
        foreach ($level1Units as $unitPath) {
            $unitName = basename($unitPath);

            // Check direct Manage/Models pattern: Modules/Units/*/Manage/Models/*
            $manageModelsPath = $unitPath . '/Manage/Models';
            if (File::exists($manageModelsPath)) {
                $directories[] = $manageModelsPath;
            }

            // Check sub-level patterns: Modules/Units/*/*/Manage/Models/*
            $subDirectories = File::directories($unitPath);
            foreach ($subDirectories as $subDir) {
                $subDirName = basename($subDir);

                // Pattern: Modules/Units/*/*/Manage/Models/*
                $subManageModelsPath = $subDir . '/Manage/Models';
                if (File::exists($subManageModelsPath)) {
                    $directories[] = $subManageModelsPath;
                }

                // Check deeper level: Modules/Units/*/*/*/Manage/Models/*
                $deeperDirectories = File::directories($subDir);
                foreach ($deeperDirectories as $deeperDir) {
                    $deeperManageModelsPath = $deeperDir . '/Manage/Models';
                    if (File::exists($deeperManageModelsPath)) {
                        $directories[] = $deeperManageModelsPath;
                    }
                }
            }
        }

        return $directories;
    }

    /**
     * Scan a directory for models that extend ApprovableModel
     */
    private function scanDirectoryForApprovableModels(string $directory): array
    {
        $models = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $path = $file->getRealPath();
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $class = $this->getClassFullName($path);

                if (!class_exists($class)) {
                    // Only require the file if it's not already loaded
                    if (!in_array($path, get_included_files())) {
                        require_once $path;
                    }
                }

                // Make sure the class extends 'EightyNine\Approvals\Models\ApprovableModel'
                if (class_exists($class) && is_subclass_of($class, 'EightyNine\Approvals\Models\ApprovableModel')) {
                    $models[$class] = $class;
                }
            }
        }

        return $models;
    }

    /**
     * Extract the full class name including the namespace from a PHP file.
     *
     * @param string $path
     * @return string
     */
    private function getClassFullName($path): string
    {
        $content = file_get_contents($path);
        $namespace = $class = '';
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
        }
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            $class = $matches[1];
        }
        return $namespace . '\\' . $class;
    }
}
