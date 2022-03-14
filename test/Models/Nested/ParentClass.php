<?php

namespace AutoMapperPlus\Test\Models\Nested;

/**
 * Class ParentClass
 *
 * @package AutoMapperPlus\Test\Models\Nested
 */
class ParentClass
{
    public $child;

    /**
     * @var AbstractPolymorphicChild[]
     */
    public $polymorphicChildren;
}
