<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;


class FreshCommand extends Command
{
    use ConfirmableTrait, Prohibitable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables and re-run all migrations';

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->isProhibited() ||
            !$this->confirmToProceed()) {
            return Command::FAILURE;
        }
        $connection_names = ['laravel', 'admin', 'manage', 'my'];
        $database = $this->input->getOption('database');

        foreach ($connection_names as $connection_name) {
            $connection_name = env('APP_ENV') == 'testing' ? 'test_' . $connection_name : $connection_name;
            $this->call('db:wipe', array_filter([
                '--database' => $connection_name,
                '--drop-views' => $this->option('drop-views'),
                '--drop-types' => $this->option('drop-types'),
                '--force' => true,
            ]));
        }


        $this->newLine();


//        foreach ($connection_names as $connection_name) {
//            try {
                $connection_name = env('APP_ENV') == 'testing' ? 'test_' . $connection_name : $connection_name;
                $this->call('migrate', array_filter([
                    '--database' => $connection_name,
//                    '--path' => $this->input->getOption('path'),
//                    '--realpath' => $this->input->getOption('realpath'),
//                    '--schema-path' => $this->input->getOption('schema-path'),
                    '--force' => true,
                    '--step' => $this->option('step'),
                ]));
//            }catch (\Exception $e){}
            echo "\n\n\n\n\nSuccessfully run\n\n\n\n\n";
//        }


        if ($this->laravel->bound(Dispatcher::class)) {
            $this->laravel[Dispatcher::class]->dispatch(
                new DatabaseRefreshed($database, $this->needsSeeding())
            );
        }

        if ($this->needsSeeding()) {
            $this->runSeeder($database);
        }

        return 0;
    }

    /**
     * Determine if the developer has requested database seeding.
     *
     * @return bool
     */
    protected function needsSeeding()
    {
        return $this->option('seed') || $this->option('seeder');
    }

    /**
     * Run the database seeder command.
     *
     * @param string $database
     * @return void
     */
    protected function runSeeder($database)
    {
        $this->call('db:seed', array_filter([
            '--database' => $database,
            '--class' => $this->option('seeder') ?: 'Database\\Seeders\\DatabaseSeeder',
            '--force' => true,
        ]));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
            ['drop-views', null, InputOption::VALUE_NONE, 'Drop all tables and views'],
            ['drop-types', null, InputOption::VALUE_NONE, 'Drop all tables and types (Postgres only)'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The path(s) to the migrations files to be executed'
            ],
            [
                'realpath',
                null,
                InputOption::VALUE_NONE,
                'Indicate any provided migration file paths are pre-resolved absolute paths'
            ],
            ['schema-path', null, InputOption::VALUE_OPTIONAL, 'The path to a schema dump file'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'],
            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder'],
            [
                'step',
                null,
                InputOption::VALUE_NONE,
                'Force the migrations to be run so they can be rolled back individually'
            ],
        ];
    }
}
