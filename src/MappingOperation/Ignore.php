<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

/**
 * Class Ignore
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
