<?php

namespace AutoMapperPlus\PrivateAccessor;

/**
 * Interface PrivateAccessorInterface
 *
 * @package AutoMapperPlus\PrivateAccessor
 */
interface PrivateAccessorInterface
{
    public function getPrivate($object, string $attribute);

    public function setPrivate($object, string $attribute, $value);
}
