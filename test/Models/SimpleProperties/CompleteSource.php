<?php

namespace AutoMapperPlus\Test\Models\SimpleProperties;

/**
 * Class CompleteSource
 *
 * @package AutoMapperPlus\Test\Models\SimpleProperties
 */
class CompleteSource
{
    public $name;

    public $anotherProperty;

    public function __construct($name = '', $anotherProperty = '')
    {
        $this->name = $name;
        $this->anotherProperty = $anotherProperty;
    }
}
