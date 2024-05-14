<?php


namespace INTCore\OneARTConsole\Commands;

use INTCore\OneARTConsole\Str;
use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Generators\FeatureGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class FeatureMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Feature in a service';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Feature';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        try {
            $domain = $this->argument('domain');
//            $title = $this->parseName($this->argument('feature'));
            $title = $this->argument('feature');

            $generator = new FeatureGenerator();
            $feature = $generator->generate($title, $domain);

            $this->info(
                'Feature class '.$feature->title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$feature->relativePath.'</comment>'."\n"
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
            ['feature', InputArgument::REQUIRED, 'The feature\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain in which the feature should be implemented.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../Generators/stubs/feature.stub';
    }

    /**
     * Parse the feature name.
     *  remove the Feature.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return Str::feature($name);
    }
}
