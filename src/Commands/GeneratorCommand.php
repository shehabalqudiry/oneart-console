<?php

namespace INTCore\OneARTConsole\Commands;

class GeneratorCommand extends IlluminateGeneratorCommand
{
    public function __construct(Filesystem $files, Generator $generator)
    {
        parent::__construct($files);

        $this->generator = $generator;
    }
}
