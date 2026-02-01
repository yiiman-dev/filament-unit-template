<?php



namespace Modules\Shell\Console\NodiShell\Categories;

use Modules\Shell\Console\NodiShell\Scripts\DtoScript;
use Modules\Shell\Console\NodiShell\Scripts\CreateResourceScript;
use NodiLabs\NodiShell\Abstracts\BaseCategory;

final class generatorCategory extends BaseCategory
{
    protected int $sortOrder = 100;

    public function getName(): string
    {
        return 'Generators';
    }

    public function getDescription(): string
    {
        return 'Scripts for Generators';
    }

    public function getIcon(): string
    {
        return '📁';
    }

    public function getColor(): string
    {
        return 'blue';
    }

    protected function loadScripts(): void
    {
        // Load scripts for this category
        $this->scripts = [
            new DtoScript()
        ];
    }
}
