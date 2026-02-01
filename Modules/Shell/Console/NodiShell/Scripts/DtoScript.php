<?php


namespace Modules\Shell\Console\NodiShell\Scripts;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use NodiLabs\NodiShell\Abstracts\BaseScript;

final class DtoScript extends BaseScript
{
    protected string $name = 'Create DTO Class';

    protected string $description = 'Create DTO Class on specific path';

    protected string $category = 'generator';

    protected bool $productionSafe = true;

    protected array $parameters = [
        [
            'name' => 'migration_path',
            'label' => 'migration path',
            'type' => 'string',
            'description' => 'Same as directory name',
            'required' => true
        ],
        [
            "name" => "dto_path",
            "label" => "dto_path",
            "description" => "Name of model file name",
            "type" => "string",
            "required" => true
        ],
        [
            "name" => 'dto_name',
            "label" => 'dto class name',
            "description" => "name of filament panel(ID)",
            "type" => 'string',
            'required' => true
        ],

    ];

    protected array $tags = ['create-dto'];



    public function execute(array $parameters = []): mixed
    {
        try {
            // Access session and variables
            $session = $parameters['_session'] ?? null;
            $variables = $parameters['_variables'] ?? [];

            $migration_path= $parameters['migration_path'];
            $dto_path= $parameters['dto_path'];
            $dto_name= $parameters['dto_name'];

            $command=sprintf("make:dto \"%s\" \"%s\"  \"%s\"",
                $migration_path,
                $dto_path,
                $dto_name
            );
            echo "php artisan ".$command;


            Artisan::call($command);

            $output = Artisan::output();

            return [
                'success' => true,
                'data' => null,
                'message' => $output,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()."\n".Collection::fromJson(json_encode($e->getTrace()))->dd(),
            ];
        }
    }
}
