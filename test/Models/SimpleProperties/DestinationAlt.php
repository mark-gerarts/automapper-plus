<?php

namespace AutoMapperPlus\Test\Models\SimpleProperties;

/**
 * Class DestinationAlt
 *
 * An alternative form of Destination. Could represent for example a DetailView
 * and a ListView.
 *
 * @package AutoMapperPlus\Test\Models\SimpleProperties
 */
class DestinationAlt
{
    public $name;

    public $anotherProperty;

    public $altProperty;

    public function __construct($name = '')
    {
        $this->name = $name;
    }
}
