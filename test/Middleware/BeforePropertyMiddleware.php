<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class BeforePropertyMiddleware implements PropertyMiddleware
{
    public function supportsMapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        if ($propertyName == 'name') {
            return Middleware::BEFORE;
        }
    }

    public function mapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        $destination->{$propertyName} = 'This should never appear';
    }
}