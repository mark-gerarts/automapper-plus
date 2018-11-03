<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Class ObjectCratePropertyWriter
 *
 * Object crates are objects like stdClass, who just accept any properties you
 * try to write to them (see #3).
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
class ObjectCratePropertyWriter implements PropertyWriterInterface
{
    /**
     * @inheritdoc
     */
    public function setProperty($object, string $propertyName, $value): void
    {
        $object->{$propertyName} = $value;
    }
}
