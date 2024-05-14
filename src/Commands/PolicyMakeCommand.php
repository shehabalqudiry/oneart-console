<?php

namespace INTCore\OneARTConsole\Commands;

use Exception;
use INTCore\OneARTConsole\Generators\PolicyGenerator;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;


/**
 * Class PolicyMakeCommand
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Commands
 */
class PolicyMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Policy.';

    /**
     * The type of class being generated
     * @var string
     */
    protected $type = 'Policy';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new PolicyGenerator();

        $name = $this->argument('policy');
        $domain = $this->argument('domain');

        try {
            $policy = $generator->generate($name, $domain);

            $this->info('Policy class created successfully.' .
                "\n" .
                "\n" .
                'Find it at <comment>' . $policy->relativePath . '</comment>' . "\n"
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
            ['policy', InputArgument::REQUIRED, 'The Policy\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain\'s name.']
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__ . '/../Generators/stubs/policy.stub';
    }
}
