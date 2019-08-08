<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Middleware\MapperMiddleware;
use AutoMapperPlus\Middleware\Middleware;

class AppendMapperMiddleware implements MapperMiddleware
{
    private $append;
    private $supportsValue;

    public function __construct(string $append = ' (append)', int $supportsValue = Middleware::AFTER)
    {
        $this->append = $append;
        $this->supportsValue = $supportsValue;
    }

    public function supportsMap($source, $destination, MappingInterface $mapping, array $context = [])
    {
        return $this->supportsValue;
    }

    public function map($source, $destination, MappingInterface $mapping, array $context = [])
    {
        $destination->name = $destination->name . $this->append;
    }
}