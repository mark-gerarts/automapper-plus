<?php

declare(strict_types=1);

namespace AutoMapperPlus\Test\Models\Nested;

abstract class AbstractPolymorphicDto
{
    public $name;

    public function __construct($name = '')
    {
        $this->name = $name;
    }
}
