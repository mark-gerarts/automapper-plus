<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Interface PropertyReaderInterface
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
interface MethodReaderInterface
{
    /**
     * @param $object
     * @param string $methodName
     * @return bool
     */
    public function hasMethod($object, string $methodName): bool;

    /**
     * @param $object
     * @param string $methodName
     * @return mixed
     */
    public function getMethod($object, string $methodName);
}
