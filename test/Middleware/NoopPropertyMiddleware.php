<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class NoopPropertyMiddleware implements PropertyMiddleware
{
    private $supportsValue;

    public function __construct($supportsValue = Middleware::OVERRIDE)
    {
        $this->supportsValue = $supportsValue;
    }

    public function supportsMapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        return $propertyName == 'name' ? $this->supportsValue : false;
    }

    public function mapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        //NOOP
    }
}