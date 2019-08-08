<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Middleware\MapperMiddleware;
use AutoMapperPlus\Middleware\Middleware;

class NoopMapperMiddleware implements MapperMiddleware
{
    private $supportsValue;

    public function __construct($supportsValue = Middleware::OVERRIDE)
    {
        $this->supportsValue = $supportsValue;
    }

    public function supportsMap($source, $destination, MappingInterface $mapping, array $context = [])
    {
        return $this->supportsValue;
    }

    public function map($source, $destination, MappingInterface $mapping, array $context = [])
    {
        //NOOP
    }
}