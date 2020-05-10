<?php


namespace AutoMapperPlus\Middleware;


use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;

class DefaultPropertyMiddleware implements PropertyMiddleware
{
    protected function doMapProperty($propertyName,
                                     $source,
                                     $destination,
                                     AutoMapperInterface $mapper,
                                     MappingInterface $mapping,
                                     MappingOperationInterface $operation,
                                     array $context)
    {
        $operation->mapProperty(
            $propertyName,
            $source,
            $destination
        );
    }

    public function mapProperty($propertyName,
                                $source,
                                $destination,
                                AutoMapperInterface $mapper,
                                MappingInterface $mapping,
                                MappingOperationInterface $operation,
                                array $context,
                                callable $next)
    {
        $this->doMapProperty($propertyName, $source, $destination, $mapper, $mapping, $operation, $context);
        $next();
    }
}