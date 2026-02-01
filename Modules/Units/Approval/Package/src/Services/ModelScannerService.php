<?php

namespace EightyNine\Approvals\Services;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ModelScannerService
{
    /**
     * Scan for models extending from a specific base model.
     *
     * @return array
     */
    public function getApprovableModels(): array
    {
        $directory = app_path('Models');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $models = [];
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            
            $path = $file->getRealPath();
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $class = $this->getClassFullName($path);
                
                if (!class_exists($class)) {
                    require_once $path; // Load the class file
                }
                
                // Make sure the class extends 'App\Models\ApprovableModel'
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
