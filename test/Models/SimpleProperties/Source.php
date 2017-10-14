<?php

namespace Test\Models\SimpleProperties;

/**
 * Class Source
 *
 * @package Test\Models\SimpleProperties
 */
class Source
{
    public $name;

    public function __construct($name = '')
    {
        $this->name = $name;
    }
}
