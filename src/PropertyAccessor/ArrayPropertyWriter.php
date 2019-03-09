<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Class ArrayPropertyWriter
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
class ArrayPropertyWriter implements PropertyWriterInterface
{
    /**
     * @inheritdoc
     */
    public function setProperty(&$object, string $propertyName, $value): void
    {
        $object[$propertyName] = $value;
    }
}
