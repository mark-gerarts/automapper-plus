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
     * @deprecated This is kept for BC reasons. It makes more sense to just
     *             implement the interface now.
     */
    public function map($source, $target, array $context = [])
    {
        if (\is_string($target)) {
            $target = new $target;
        }

        return $this->mapToObject($source, $target, $context);
    }

    /**
     * @inheritdoc
     */
    abstract public function mapToObject(
        $source,
        $destination,
        array $context = []
    );
}
