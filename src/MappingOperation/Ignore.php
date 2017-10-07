<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

/**
 * Class Ignore
 *
 * This operation can be used to specify a property to be ignored.
 *
 * @package AutoMapperPlus\MappingOperation
 */
class Ignore implements MappingOperationInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke
    (
        $from,
        $to,
        string $propertyName,
        AutoMapperConfigInterface $config
    ): void
    {
        // Do nothing.
    }
}
