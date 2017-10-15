<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Configuration;

/**
 * Interface MappingOperationInterface
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface MappingOperationInterface
{
    /**
     * @param string $propertyName
     * @param $source
     * @param $destination
     * @return void
     */
    public function mapProperty(string $propertyName, $source, $destination): void;

    /**
     * @param Configuration $config
     */
    public function setConfig(Configuration $config): void;
}
