<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Middleware\MapperMiddleware;
use AutoMapperPlus\Middleware\Middleware;

class BeforeMapperMiddleware implements MapperMiddleware
{
    public function supportsMap($source, $destination, MappingInterface $mapping, array $context = [])
    {
        return Middleware::BEFORE;
    }

    public function map($source, $destination, MappingInterface $mapping, array $context = [])
    {
        $destination->name = 'This should never appear';
    }
}