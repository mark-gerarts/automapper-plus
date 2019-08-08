<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class ValuePropertyMiddleware implements PropertyMiddleware
{
    private $value;
    private $supportsValue;

    public function __construct($value = 'property middleware value', int $supportsValue = Middleware::AFTER)
    {
        $this->value = $value;
        $this->supportsValue = $supportsValue;
    }

    public function supportsMapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        return $propertyName == 'name' ? $this->supportsValue : false;
    }

    public function mapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        $destination->name = $this->value;
    }
}