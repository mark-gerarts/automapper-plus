<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;

/**
 * Interface MappingOperationInterface
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface MappingOperationInterface
{
    /**
     * @param string $propertyName
     * @param mixed $source
     * @param mixed $destination
     * @return void
     */
    public function mapProperty(string $propertyName, $source, &$destination): void;

    /**
     * @param Options $options
     */
    public function setOptions(Options $options): void;
}
