<?php


namespace AutoMapperPlus\Middleware;


use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\MappingInterface;

/**
 * Middleware to intercept mapping of an object.
 */
interface MapperMiddleware extends Middleware
{
    /**
     * Perform a custom object mapping job.
     *
     * @param $source
     * @param $destination
     * @param AutoMapperInterface $mapper
     * @param MappingInterface $mapping
     * @param array $context
     * @param callable $next
     * @return mixed
     */
    public function map(
        $source,
        $destination,
        AutoMapperInterface $mapper,
        MappingInterface $mapping,
        array $context,
        callable $next
    );
}