<?php

namespace AutoMapperPlus\Test\Models\SpecialConstructor;

class Destination
{
    public $constructorRan = false;

    function __construct()
    {
        $this->constructorRan = true;
    }
}
