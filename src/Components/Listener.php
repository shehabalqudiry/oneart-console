<?php

namespace INTCore\OneARTConsole\Components;

class Listener extends Component
{
    public function __construct($title, $namespace, $file, $path, $relativePath, Domain $service, $content)
    {
        $className = str_replace(' ', '', $title).'Listener';
        $this->setAttributes([
            'title' => $title,
            'className' => $className,
            'namespace' => $namespace,
            'file' => $file,
            'realPath' => $path,
            'relativePath' => $relativePath,
            'service' => $service,
            'content' => $content,
        ]);
    }
}

