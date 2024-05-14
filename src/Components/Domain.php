<?php

namespace INTCore\OneARTConsole\Components;

use Illuminate\Support\Str;
use Illuminate\Support\Str as StrHelper;

class Domain extends Component
{
    public function __construct($name, $realPath, $relativePath)
    {
        $this->setAttributes([
            'name' => $name,
            'slug' => StrHelper::snake($name),
            'realPath' => $realPath,
            'relativePath' => $relativePath,
        ]);
    }
}
