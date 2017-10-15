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
     * @return mixed
     */
    public function getProperty($object, string $propertyName);

    /**
     * @param $object
     * @param string $propertyName
     * @param $value
     */
    public function setProperty($object, string $propertyName, $value): void;
}
