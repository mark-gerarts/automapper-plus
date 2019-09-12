<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class PrependPropertyMiddleware implements PropertyMiddleware
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

    public function mapProperty($propertyName,
                                $source,
                                $destination,
                                AutoMapperInterface $mapper,
                                MappingInterface $mapping,
                                MappingOperationInterface $operation,
                                array $context,
                                callable $next)
    {
        if (in_array($propertyName, $this->propertyNames)) {
            $destination->{$propertyName} = $this->value . $destination->{$propertyName} ;
        }
        $next();
    }
}