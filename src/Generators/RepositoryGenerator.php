<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Components\Repository;
use INTCore\OneARTConsole\Str;

class RepositoryGenerator extends Generator
{
    public function generate($name, $domain)
    {
        $repository = Str::repository($name);
        $domain = Str::domain($domain);
        $path = $this->findRepositoryPath($domain, $name);

        if ($this->exists($path)) {
            throw new Exception('Repository already exists');

            return false;
        }

        // Make sure the domain directory exists
        $this->createDomainDirectory($domain);

        // Create the job
        $namespace = $this->findDomainRepositoryNamespace($domain, $name);
        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{repository}}', '{{namespace}}'],
            [$repository, $namespace],
            $content
        );

        $this->createFile($path, $content);

        return new Repository(
            $repository,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($domain) ? $this->findDomain($domain) : null,
            $content
        );
    }

    /**
     * Create domain directory.
     *
     * @param string $domain
     */
    private function createDomainDirectory($domain)
    {
        $this->createDirectory($this->findDomainPath($domain) . '/Repositories');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        $stubName = '/stubs/repository.stub';
        return __DIR__ . $stubName;
    }

}
