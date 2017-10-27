<?php

namespace AutoMapperPlus\Test\CustomMappingOperations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;

class BasicOperation implements MappingOperationInterface
{
    public function mapProperty(string $propertyName, $source, $destination): void
    {
        $destination->name = 'BasicOperation';
    }

    public function setOptions(Options $options): void
    {
        //
    }
}
