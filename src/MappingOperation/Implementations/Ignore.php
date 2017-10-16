<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;

/**
 * Class Ignore
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class Ignore implements MappingOperationInterface
{
    /**
     * @inheritdoc
     */
    public function mapProperty(string $propertyName, $source, $destination): void
    {
        // Don't do anything.
    }

    /**
     * @inheritdoc
     */
    public function setOptions(Options $options): void
    {
        // We don't need any configuration.
    }

}
