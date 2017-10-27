<?php

namespace AutoMapperPlus\Test\CustomMappingOperations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Reversible;

class ReversibleOperation implements MappingOperationInterface, Reversible
{
    private $name;

    public function __construct($name = "ReversibleNormal")
    {
        $this->name = $name;
    }

    public function mapProperty(string $propertyName, $source, $destination): void
    {
        $destination->name = $this->name;
    }

    public function setOptions(Options $options): void
    {
        //
    }

    public function getReverseOperation
    (
        string $originalProperty,
        Options $options
    ): MappingOperationInterface
    {
        return new static("ReversibleReversed");
    }

    public function getReverseTargetPropertyName
    (
        string $originalProperty,
        Options $options
    ): string
    {
        return "name";
    }
}
