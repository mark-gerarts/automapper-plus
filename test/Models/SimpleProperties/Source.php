<?php

namespace AutoMapperPlus\Test\Models\SimpleProperties;

/**
 * Class Source
 *
 * @package AutoMapperPlus\Test\Models\SimpleProperties
 */
class Source
{
    public $name;

    public function __construct($name = '')
    {
        $this->name = $name;
    }
}
