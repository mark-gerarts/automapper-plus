<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Interface PropertyReaderInterface
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
interface PropertyReaderInterface
{
    /**
     * @param mixed $object
     * @param string $propertyName
     * @return bool
     */
    public function hasProperty($object, string $propertyName): bool;

    /**
     * @param mixed $object
     * @param string $propertyName
     * @return mixed
     */
    public function getProperty($object, string $propertyName);

    /**
     * Returns a list of property names available on the object.
     *
     * @param mixed $object
     * @return string[]
     */
    public function getPropertyNames($object): array;
}
