<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class AnwserToUniverseMiddleware implements PropertyMiddleware
{
    public function mapProperty($propertyName,
                                $source,
                                $destination,
                                AutoMapperInterface $mapper,
                                MappingInterface $mapping,
                                MappingOperationInterface $operation,
                                array $context,
                                callable $next)
    {
        if ($propertyName === 'id') {
            $defaultValue = $mapping->getOptions()->getPropertyReader()->getProperty($destination, $propertyName);
            if ($defaultValue === NULL) {
                $mapping->getOptions()->getPropertyWriter()->setProperty($destination, $propertyName, 42);
            }
        }
        $next();
    }
}