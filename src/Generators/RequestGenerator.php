<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Components\Request;
use INTCore\OneARTConsole\Str;

/**
 * Class RequestGenerator
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Generators
 */
class RequestGenerator extends Generator
{
    /**
     * Generate the file.
     *
     * @param string $name
     * @param string $domain
     * @return Request|bool
     * @throws Exception
     */
    public function generate($name, $domain)
    {
        $request = Str::request($name);
        $domain = Str::domain($domain);
        $path = $this->findRequestPath($domain, $name);

        if ($this->exists($path)) {
            throw new Exception('Request already exists');

            return false;
        }

        $namespace = $this->findRequestsNamespace($domain, $name);

        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{request}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$request, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        return new Request(
            $request,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            $this->findDomain($domain),
            $content
        );
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
