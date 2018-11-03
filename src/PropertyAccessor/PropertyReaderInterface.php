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
     * @param $object
     * @param string $propertyName
     * @return bool
     */
    public function hasProperty($object, string $propertyName): bool;

    /**
     * @param $object
     * @param string $propertyName
     * @return mixed
     */
    public function getProperty($object, string $propertyName);

    /**
     * Returns a list of property names available on the object.
     *
     * @param object $object
     * @return string[]
     */
    public function getPropertyNames($object): array;
}
