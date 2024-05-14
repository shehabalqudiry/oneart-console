<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Components\Domain;
use INTCore\OneARTConsole\Str;
use Illuminate\Support\Str as StrHelper;

class DomainGenerator extends Generator
{
    /**
     * The directories to be created under the Domain directory.
     *
     * @var array
     */
    protected $directories = [
        'Console/',
        'config/',
        'database/',
        'database/factories/',
        'database/migrations/',
        'database/seeds/',
        'Http/',
        'Enum/',
        'Repositories/',
        'Mails/',
        'Notifications/',
        'Http/Controllers/',
        'Http/Middleware/',
        'Http/Requests/',
        "Http/Resources/",
        'Providers/',
        'Features/',
        'resources/',
        'resources/lang/',
        'resources/views/',
        'routes',
        'Tests/',
        'Tests/Features/',
    ];


    public function generate($name)
    {
        $name = Str::domain($name);
        $slug = StrHelper::snake($name);
        $path = $this->findDomainPath($name);

        if ($this->exists($path)) {
            throw new Exception('Domain already exists!');

            return false;
        }

        // create domain directory
        $this->createDirectory($path);
        // create .gitkeep file in it
        $this->createFile($path.'/.gitkeep');

        $this->createDomainDirectories($path);

        $this->addDomainProviders($name, $slug, $path);

        $this->addRoutesFiles($name, $slug, $path);

        $this->addConfigFile($name, $slug, $path);

        // $this->addWelcomeViewFile($path);

        $this->addModelFactory($path);

        return new Domain(
            $name,
            $slug,
            $path,
            $this->relativeFromReal($path)
        );
    }

    private function getDirectories()
    {
        $config_extra_dirs = config('OneART.extra_dir');
        return array_merge($this->directories, $config_extra_dirs);
    }

    /**
     * Create the default directories at the given domain path.
     *
     * @param  string $path
     *
     * @return void
     */
    public function createDomainDirectories($path)
    {
        foreach ($this->getDirectories() as $directory) {
            $this->createDirectory($path.'/'.$directory);
            $this->createFile($path.'/'.$directory.'/.gitkeep');
        }
    }

    /**
     * Add the corresponding domain provider for the created domain.
     *
     * @param string $name
     * @param string $path
     *
     * @return bool
     */
    public function addDomainProviders($name, $slug, $path)
    {
        $namespace = $this->findDomainNamespace($name).'\\Providers';

        $this->createRegistrationServiceProvider($name, $path, $slug, $namespace);
        
        $this->createRepositoryServiceProvider($name, $path, $slug, $namespace);

        $this->createRouteServiceProvider($name, $path, $slug, $namespace);

        $this->createAuthServiceProvider($name, $path, $slug, $namespace);
    }

    /**
     * Create the domain provider that registers this domain.
     *
     * @param  string $name
     * @param  string $path
     */
    public function createRegistrationServiceProvider($name, $path, $slug, $namespace)
    {
        $content = file_get_contents(__DIR__.'/stubs/serviceprovider.stub');
        $content = str_replace(
            ['{{name}}', '{{slug}}', '{{namespace}}', '{{config_name}}'],
            [$name, $slug, $namespace, strtolower($name)],
            $content
        );

        $this->createFile($path.'/Providers/'.$name.'ServiceProvider.php', $content);
    }

    /**
     * Create the domain provider that registers this domain.
     *
     * @param  string $name
     * @param  string $path
     */
    public function createRepositoryServiceProvider($name, $path, $slug, $namespace)
    {
        $content = file_get_contents(__DIR__.'/stubs/repositoryserviceprovider.stub');

        $content = str_replace(
            ['{{name}}', '{{slug}}', '{{namespace}}'],
            [$name, $slug, $namespace],
            $content
        );

        $this->createFile($path.'/Providers/RepositoryServiceProvider.php', $content);
    }

    

    /**
     * Create the routes service provider file.
     *
     * @param  string $name
     * @param  string $path
     * @param  string $slug
     * @param  string $namespace
     */
    public function createRouteServiceProvider($name, $path, $slug, $namespace)
    {
        $domainNamespace = $this->findDomainNamespace($name);
        $controllers = $domainNamespace.'\Http\Controllers';
        $foundation = $this->findFoundationNamespace();

        $content = file_get_contents(__DIR__.'/stubs/routeserviceprovider.stub');
        $content = str_replace(
            ['{{name}}', '{{namespace}}', '{{controllers_namespace}}', '{{foundation_namespace}}'],
            [$name, $namespace, $controllers, $foundation],
            $content
        );

        $this->createFile($path.'/Providers/RouteServiceProvider.php', $content);
    }


    /**
     * Create the auth service provider file.
     *
     * @param  string $name
     * @param  string $path
     * @param  string $slug
     * @param  string $namespace
     */
    public function createAuthServiceProvider($name, $path, $slug, $namespace)
    {
        $domainNamespace = $this->findDomainNamespace($name);

        $content = file_get_contents(__DIR__.'/stubs/authserviceprovider.stub');
        $content = str_replace(
            ['{{name}}', '{{namespace}}'],
            [$name, $namespace],
            $content
        );

        $this->createFile($path.'/Providers/AuthServiceProvider.php', $content);
    }



     /**
     * Add the routes files.
     *
     * @param string $name
     * @param string $slug
     * @param string $path
     */
    public function addRoutesFiles($name, $slug, $path)
    {
        $controllers = 'src/Domains/' . $name . '/Http/Controllers';

        $contentApi = file_get_contents(__DIR__ . '/stubs/routes-api.stub');
        $contentApi = str_replace(['{{name}}', '{{slug}}', '{{controllers_path}}'], [$name, $slug, $controllers], $contentApi);

        $contentWeb = file_get_contents(__DIR__ . '/stubs/routes-web.stub');
        $contentWeb = str_replace(['{{name}}', '{{slug}}', '{{controllers_path}}'], [$name, $slug, $controllers], $contentWeb);

        $this->createFile($path . '/routes/api.php', $contentApi);
        $this->createFile($path . '/routes/web.php', $contentWeb);

        unset($contentApi, $contentWeb);
    }


    /**
     * Add the config file.
     *
     * @param string $name
     * @param string $slug
     * @param string $path
     */
    public function addConfigFile($name, $slug, $path)
    {
        $config_template = file_get_contents(__DIR__ . '/stubs/config.stub');

        $contentApi = str_replace(['{{name}}'], [$name], $config_template);

        $config_file_name = strtolower($name);
        $this->createFile($path . "/config/{$config_file_name}.php", $contentApi);

        unset($contentApi, $contentWeb);
    }


    

    /**
     * Add the welcome view file.
     *
     * @param string $path
     */
    public function addWelcomeViewFile($path)
    {
        $this->createFile(
            $path.'/resources/views/welcome.blade.php',
            file_get_contents(__DIR__.'/stubs/welcome.blade.stub')
        );
    }

    /**
     * Add the ModelFactory file.
     *
     * @param string $path
     */
    public function addModelFactory($path)
    {
        $modelFactory = file_get_contents(__DIR__ . '/stubs/model-factory.stub');
        $this->createFile($path . '/database/factories/ModelFactory.php', $modelFactory);

        unset($modelFactory);
    }
}
