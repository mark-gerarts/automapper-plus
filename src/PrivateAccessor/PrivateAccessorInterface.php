<?php

namespace AutoMapperPlus\PrivateAccessor;

/**
 * Interface PrivateAccessorInterface
 *
 * @package AutoMapperPlus\PrivateAccessor
 */
interface PrivateAccessorInterface
{
    /**
     * Retrieves the value of an object's private property.
     *
     * @param $object
     * @param string $attribute
     * @return mixed
     */
    public function getPrivate($object, string $attribute);

    /**
     * Sets the value of an object's private property.
     *
     * @param $object
     * @param string $attribute
     * @param $value
     */
    public function setPrivate($object, string $attribute, $value): void;
}
