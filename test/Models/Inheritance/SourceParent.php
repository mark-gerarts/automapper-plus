<?php

namespace AutoMapperPlus\Test\Models\Inheritance;

class SourceParent
{
    private $name;

    public function __construct($name = '')
    {
        $this->name = $name;
    }
}
