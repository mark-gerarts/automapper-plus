<?php


namespace AutoMapperPlus\Middleware;


use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;

/**
 * Middleware to intercept the mapping of a property.
 *
 * @see AutoMapperConfigInterface::registerMiddlewares()
 */
interface PropertyMiddleware extends Middleware
{
    /**
     * Perform a custom property mapping job.
     *
     * @param $propertyName
     * @param $source
     * @param $destination
     * @param AutoMapperInterface $mapper
     * @param MappingInterface $mapping
     * @param MappingOperationInterface $operation
     * @param array $context
     * @param callable $next
     * @return mixed
     */
    public function mapProperty(
        $propertyName,
        $source,
        $destination,
        AutoMapperInterface $mapper,
        MappingInterface $mapping,
        MappingOperationInterface $operation,
        array $context,
        callable $next
    );
}