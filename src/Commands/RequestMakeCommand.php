<?php

namespace INTCore\OneARTConsole\Commands;

use Exception;
use INTCore\OneARTConsole\Generators\RequestGenerator;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;


/**
 * Class RequestMakeCommand
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Commands
 */
class RequestMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Request in a specific service.';

    /**
     * The type of class being generated
     * @var string
     */
    protected $type = 'Request';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new RequestGenerator();

        $name = $this->argument('request');
        $service = $this->argument('service');

        try {
            $request = $generator->generate($name, $service);
            $this->info('Request class created successfully.' .
                "\n" .
                "\n" .
                'Find it at <comment>' . $request->relativePath . '</comment>' . "\n"
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
    public function getArguments()
    {
        return [
            ['request', InputArgument::REQUIRED, 'The Request\'s name.'],
            ['service', InputArgument::REQUIRED, 'The Service\'s name.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__ . '/../Generators/stubs/request.stub';
    }
}
