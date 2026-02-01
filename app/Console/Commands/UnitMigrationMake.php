<?php

namespace App\Console\Commands;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Database\Console\Migrations\TableGuesser;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * This class if singleton and configured on appProvider
 * @see  \App\Providers\AppServiceProvider
 */
#[AsCommand('unit:make-migration')]
class UnitMigrationMake extends BaseCommand implements PromptsForMissingInput
{
    protected $name='unit:make-migration';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'unit:make-migration {name : The name of the migration} {unit : unit name - if is dipped use dot to deep directory}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration (Deprecated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     *
     * @deprecated Will be removed in a future Laravel version.
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @param  \Illuminate\Support\Composer  $composer
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $name = Str::snake(trim($this->input->getArgument('name')));


        $table = $this->input->getOption('table');

        $create = $this->input->getOption('create') ?: false;

        // If no table was given as an option but a create option is given then we
        // will use the "create" option as the table name. This allows the devs
        // to pass a table name into this option as a short-cut for creating.
        if (! $table && is_string($create)) {
            $table = $create;

            $create = true;
        }

        // Next, we will attempt to guess the table name if this the migration has
        // "create" in the name. This will allow us to provide a convenient way
        // of creating migrations that create new tables for the application.
        if (! $table) {
            [$table, $create] = TableGuesser::guess($name);
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, $table, $create);
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $name
     * @param  string  $table
     * @param  bool  $create
     * @return void
     */
    protected function writeMigration($name, $table, $create)
    {
        $file = $this->creator->create(
            $name, $this->getMigratePath(), $table, $create
        );

        $this->components->info(sprintf('Migration [%s] created successfully.', $file));
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigratePath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                ? $this->laravel->basePath().'/'.$targetPath
                : $targetPath;
        }

        return $this->getUnitMigrationPath();
    }

    protected function getUnitMigrationPath():string
    {
        $unit = Str::pascal(trim($this->input->getArgument('unit')));
        $unit = str_replace('.', DIRECTORY_SEPARATOR, $unit);
        $path= $this->laravel->basePath().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.'Units/'.$unit.DIRECTORY_SEPARATOR.'database/migrations';
        if (realpath($path)){
            return $path;
        }else{
            throw new \Exception($path.' Based on unit name: '.$this->input->getArgument('unit').' Not found.');
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => ['What should the migration be named?', 'E.g. create_flights_table'],
        ];
    }
}
