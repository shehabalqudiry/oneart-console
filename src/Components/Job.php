<?php

namespace INTCore\OneARTConsole\Components;

class Job extends Component
{
    public function __construct($title, $namespace, $file, $path, $relativePath, Domain $service, $content)
    {
        $className = str_replace(' ', '', $title).'Job';
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

