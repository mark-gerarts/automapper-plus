<?php

namespace AutoMapperPlus\CustomMapper;

use AutoMapperPlus\MapperInterface;

/**
 * Interface CustomMapperInterface
 *
 * @package AutoMapperPlus\CustomMapper
 */
abstract class CustomMapper implements MapperInterface
{
    /**
     * @inheritdoc
     *
     * @psalm-suppress TooManyArguments
     *   Psalm borks on the missing $context on the interface, which is there
     *   because of backwards compatibility.
     */
    public function map($source, string $targetClass)
    {
        $destination = new $targetClass;

        // We use func_get_args instead of using a function parameter to
        // maintain backwards compatibility. This will change in v2.0.
        $context = func_get_args()[2] ?? [];

        return $this->mapToObject($source, $destination, $context);
    }

    /**
     * @inheritdoc
     */
    abstract public function mapToObject($source, $destination);
}
