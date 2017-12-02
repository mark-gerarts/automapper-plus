<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Interface PropertyAccessorInterface
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
interface PropertyAccessorInterface
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
     * @param $object
     * @param string $propertyName
     * @param $value
     */
    public function setProperty($object, string $propertyName, $value): void;

    /**
     * Returns a list of property names available on the object.
     *
     * @param object $object
     * @return string[]
     */
    public function getPropertyNames($object): array;
}
