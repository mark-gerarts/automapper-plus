<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Interface PropertyWriterInterface
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
interface PropertyWriterInterface
{
    /**
     * @param mixed $object
     * @param string $propertyName
     * @param mixed $value
     */
    public function setProperty(&$object, string $propertyName, $value): void;
}
