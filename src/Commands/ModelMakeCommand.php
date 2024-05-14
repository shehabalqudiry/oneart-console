<?php

namespace INTCore\OneARTConsole\Commands;

use Exception;
use INTCore\OneARTConsole\Generators\ModelGenerator;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str as StrHelper;


/**
 * Class ModelMakeCommand
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Commands
 */
class ModelMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model {--m|migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Model.';

    /**
     * The type of class being generated
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $modelGenerator = new ModelGenerator();
        $domain = $this->argument('domain');
        $name = $this->argument('model');
        $isMigratable = $this->option('migration');
        try {
            $model = $modelGenerator->generate($name, $domain);

            $this->info('Model class created successfully.' .
                "\n" .
                "\n" .
                'Find it at <comment>' . $model->relativePath . '</comment>' . "\n"
            );
            if($isMigratable) {
                $migration = StrHelper::snake($name);
                $migration = StrHelper::plural($migration);

                $migration_name_to_array = explode('/', $migration);
                $qualified_migration_name = end($migration_name_to_array);
                $qualified_migration_name = $qualified_migration_name[0] == '_' ?
                    ltrim($qualified_migration_name, '_') :
                    $qualified_migration_name;

                $qualified_migration_name = "create_{$qualified_migration_name}_table";

                $path = $this->relativeFromReal($this->findDomainPath($domain) . "/database/migrations");
                $output = shell_exec('php artisan make:migration ' . $qualified_migration_name . ' --path=' . $path);
                $this->info($output);
            }
        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'The Model\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The Domain\'s name.']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['migration', 'm', InputOption::VALUE_NONE, 'Whether to create model with migration or not.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__ . '/../Generators/stubs/model.stub';
    }
}
