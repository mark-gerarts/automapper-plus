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
     */
    public function map($source, string $targetClass)
    {
        $destination = new $targetClass;

        return $this->mapToObject($source, $destination);
    }

    /**
     * @inheritdoc
     */
    abstract public function mapToObject($source, $destination);
}
