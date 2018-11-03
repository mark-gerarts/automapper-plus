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
     * @param $object
     * @param string $propertyName
     * @param $value
     */
    public function setProperty($object, string $propertyName, $value): void;
}
