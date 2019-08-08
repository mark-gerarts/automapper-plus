<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Middleware\MapperMiddleware;
use AutoMapperPlus\Middleware\Middleware;

class ValueMapperMiddleware implements MapperMiddleware
{
    private $value;
    private $supportsValue;

    public function __construct($value = 'mapper middleware value', int $supportsValue = Middleware::AFTER)
    {
        $this->value = $value;
        $this->supportsValue = $supportsValue;
    }

    public function supportsMap($source, $destination, MappingInterface $mapping, array $context = [])
    {
        return $this->supportsValue;
    }

    public function map($source, $destination, MappingInterface $mapping, array $context = [])
    {
        $destination->name = $this->value;
    }
}