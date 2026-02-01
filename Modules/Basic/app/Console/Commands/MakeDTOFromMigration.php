<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/5/25, 12:17 PM
 */

namespace Modules\Basic\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Traits\PathNamespace;

class MakeDTOFromMigration extends Command
{
    use PathNamespace;
    protected $signature = 'make:dto
        {migration_path : مسیر فایل مایگریشن}
        {dto_path : مسیر پوشه DTO مقصد}
        {dto_name : نام کلاس DTO}';

    protected $description = 'ساخت کلاس DTO از فایل مایگریشن همراه با DocBlock و رول‌های اولیه validation';

    public function handle()
    {
        $migrationPath = $this->argument('migration_path');
        $dtoPath = rtrim($this->argument('dto_path'), '/');
        $dtoName = $this->argument('dto_name');
        $namespace=str($this->path_namespace(
            $this->clean_path($dtoPath)
        ))
        ->after('\Modules')->replaceFirst("\\", '')->toString();
        if (!File::exists($migrationPath)) {
            $this->error("Migration not found: $migrationPath");
            return 1;
        }

        $migration = File::get($migrationPath);
        preg_match_all(
            "/->(string|integer|enum|boolean|json|text|float|double|date|datetime|timestamp|uuid|unsignedBigInteger)\('([^']+)'\)(?:->comment\('([^']+)'\))?/",
            $migration, $matches, PREG_SET_ORDER
        );

        $docBlock = '';
        $fieldsBlock = "            'id' => '',\n";
        $rulesBlock = "            'id' => [\$this->string()],\n";

        foreach ($matches as $match) {
            [$_, $type, $name, $comment] = array_pad($match, 4, '');
            $phpType = match($type) {
                'string', 'text', 'date', 'datetime', 'timestamp','uuid','enum' => 'string',
                'integer','unsignedBigInteger' => 'int',
                'float', 'double' => 'float',
                'boolean' => 'bool',
                'json' => 'array',
                default => 'mixed'
            };

            $docBlock .= " * @property {$phpType} \${$name}" . ($comment ? " {$comment}" : '') . "\n";
            $fieldsBlock .= "            '{$name}' => '',\n";

            $rules = ["\$this->required()", "\$this->{$type}()"];
            if ($type === 'string') {
                $rules[] = "\$this->max(255)";
            }

            $rulesBlock .= "            '{$name}' => [" . implode(', ', $rules) . "],\n";
        }

        $dtoCode = <<<PHP
<?php

namespace $namespace;
use Modules\Basic\BaseKit\DTO\BaseDTO;
/**
 * @property string \$id
{$docBlock} */
class {$dtoName} extends BaseDTO
{
    public function __construct()
    {
        \$this->fields = [
{$fieldsBlock}        ];
    }

    public function rules(): array
    {
        return [
{$rulesBlock}        ];
    }
}
PHP;

        File::ensureDirectoryExists($dtoPath);
        $file = "{$dtoPath}/{$dtoName}.php";
        File::put($file, $dtoCode);

        $this->info("✅ DTO generated at: $file");
        return 0;
    }
}
