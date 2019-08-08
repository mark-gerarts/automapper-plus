<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class SkipPropertyMiddleware implements PropertyMiddleware
{
    public function supportsMapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        return $propertyName == 'name' ? Middleware::SKIP : true;
    }

    public function mapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        $destination->{$propertyName} = 'This should happen unless it is name property';
    }
}