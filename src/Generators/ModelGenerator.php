<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Str;
use INTCore\OneARTConsole\Components\Model;


/**
 * Class ModelGenerator
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Generators
 */
class ModelGenerator extends Generator
{
    /**
     * Generate the file.
     *
     * @param $name
     * @param $domain
     * @return Model|bool
     * @throws Exception
     */
    public function generate($name, $domain)
    {
        $model = Str::model($name);
        $path = $this->findModelPath($domain, $name);

        if ($this->exists($path)) {
            throw new Exception('Model already exists');

            return false;
        }

        $namespace = $this->findModelNamespace($domain, $name);

        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{model}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$model, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        return new Model(
            $model,
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
        return __DIR__ . '/../Generators/stubs/model.stub';
    }
}
