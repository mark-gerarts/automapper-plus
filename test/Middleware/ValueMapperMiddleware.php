<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Middleware\MapperMiddleware;

class ValueMapperMiddleware implements MapperMiddleware
{
    private $value;

    /**
     * @var array
     */
    private $propertyNames;

    public function __construct($value, ...$propertyNames)
    {
        $this->value = $value;
        $this->propertyNames = $propertyNames;
    }

    public function map($source,
                        $destination,
                        AutoMapperInterface $mapper,
                        MappingInterface $mapping,
                        array $context,
                        callable $next)
    {
        foreach ($this->propertyNames as $propertyName) {
            $destination->{$propertyName} = $this->value;
        }
        $next();
    }
}