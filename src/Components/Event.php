<?php

namespace INTCore\OneARTConsole\Components;

class Event extends Component
{
    public function __construct($title, $namespace, $file, $path, $relativePath, Domain $service, $content)
    {
        $className = str_replace(' ', '', $title).'Event';
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

