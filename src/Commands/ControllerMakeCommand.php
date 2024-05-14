<?php

namespace INTCore\OneARTConsole\Commands;

use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use INTCore\OneARTConsole\Generators\ControllerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Support\Str as StrHelper;

class ControllerMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource Controller class in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new ControllerGenerator();

        $domain = $this->argument('domain');
        $name = $this->argument('controller');

        try {
            $controller = $generator->generate($name, $domain, $this->option('plain'));
            $this->info('Controller class created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$controller.'</comment>'."\n"
            );
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The controller\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain in which the controller should be generated.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['plain', null, InputOption::VALUE_NONE, 'Generate an empty controller class.'],
        ];
    }

    /**
     * Parse the feature name.
     *  remove the Controller.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return StrHelper::studly(preg_replace('/Controller(\.php)?$/', '', $name).'Controller');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('plain')) {
            return __DIR__.'/../Generators/stubs/controller.plain.stub';
        }

        return __DIR__.'/../Generators/stubs/controller.stub';
    }
}
