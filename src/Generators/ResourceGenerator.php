<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Str;
use INTCore\OneARTConsole\Components\Job;
use Illuminate\Support\Str as StrHelper;
use INTCore\OneARTConsole\Components\Resource;

class ResourceGenerator extends Generator
{
    public function generate($name, $domain, $isCollection = false)
    {

        $resource = $this->parseName($name, $isCollection);
        $domain = Str::domain($domain);
        $path = $this->findResourcePath($domain, $name);
        if ($this->exists($path)) {
            throw new Exception('Resource already exists');

            return false;
        }

        // Make sure the domain directory exists
        $this->createDomainDirectory($domain);

        // Create the Resource
        $namespace = $this->findDomainResourceNamespace($domain, $name);
        $content = file_get_contents($this->getStub($isCollection));
        $content = str_replace(
            ['{{resource}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$resource, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        return new Resource(
            $resource,
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
        $this->createDirectory($this->findDomainPath($domain).'/Http/Resources');
    }

    /**
     * Get the stub file for the generator.
     *
     * @param bool $isCollection
     * @return string
     */
    public function getStub($isCollection = false)
    {
        $stubName;
        if ($isCollection) {
            $stubName = '/stubs/resource-collection.stub';
        } else {
            $stubName = '/stubs/resource.stub';
        }
        return __DIR__.$stubName;
    }

    /**
     * Parse the job name.
     *  remove the Job.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     * @param bool $isCollection
     * @return string
     */
    protected function parseName($name, $isCollection = false)
    {
        return Str::resource($name, $isCollection);
    }

}
