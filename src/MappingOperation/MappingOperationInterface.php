<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

/**
 * Interface MappingOperationInterface
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface MappingOperationInterface
{
    /**
     * @param $from
     * @param $to
     * @param string $propertyName
     * @param AutoMapperConfigInterface $config
     */
    public function __invoke(
        $from,
        $to,
        string $propertyName,
        AutoMapperConfigInterface $config
    ): void;
}
