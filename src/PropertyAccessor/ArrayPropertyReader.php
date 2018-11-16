<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Class ArrayPropertyAccessor
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
class ArrayPropertyReader implements PropertyReaderInterface
{
    /**
     * @inheritdoc
     */
    public function hasProperty($array, string $propertyName): bool
    {
        return array_key_exists($propertyName, $array);
    }

    /**
     * @inheritdoc
     */
    public function getProperty($array, string $propertyName)
    {
        return $array[$propertyName];
    }

    /**
     * @inheritdoc
     *
     * @param array $array
     */
    public function getPropertyNames($array): array
    {
        return array_keys($array);
    }
}
