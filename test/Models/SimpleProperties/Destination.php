<?php

namespace AutoMapperPlus\Test\Models\SimpleProperties;

/**
 * Class Destination
 *
 * @package AutoMapperPlus\Test\Models\SimpleProperties
 */
class Destination
{
    public $name;

    public $anotherProperty;

    public function __construct($name = '')
    {
        $this->name = $name;
    }
}
