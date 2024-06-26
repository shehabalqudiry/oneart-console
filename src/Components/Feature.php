<?php

namespace INTCore\OneARTConsole\Components;

class Feature extends Component
{
    public function __construct($title, $file, $realPath, $relativePath, Domain $service = null, $content = '')
    {
        $className = str_replace(' ', '', $title).'Feature';

        $this->setAttributes([
            'title' => $title,
            'className' => $className,
            'service' => $service,
            'file' => $file,
            'realPath' => $realPath,
            'relativePath' => $relativePath,
            'content' => $content,
        ]);
    }

    // public function toArray()
    // {
    //     $attributes = parent::toArray();
    //
    //     // real path not needed
    //     unset($attributes['realPath']);
    //
    //     // map the service object to its name
    //     $attributes['service'] = $attributes['service']->name;
    //
    //     return $attributes;
    // }
}
