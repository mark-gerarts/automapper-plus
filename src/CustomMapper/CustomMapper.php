<?php

namespace AutoMapperPlus\CustomMapper;

use AutoMapperPlus\MapperInterface;

/**
 * Class CustomMapper
 *
 * @package AutoMapperPlus\CustomMapper
 */
abstract class CustomMapper implements MapperInterface
{
    /**
     * @inheritdoc
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
