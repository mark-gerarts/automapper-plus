<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class AppendPropertyMiddleware implements PropertyMiddleware
{
    private $append;
    private $supportsValue;

    public function __construct($append = ' (append)', int $supportsValue = Middleware::AFTER)
    {
        $this->append = $append;
        $this->supportsValue = $supportsValue;
    }

    public function supportsMapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        return $propertyName == 'name' ? $this->supportsValue : Middleware::SKIP;
    }

    public function mapProperty($propertyName, $source, $destination, MappingInterface $mapping, MappingOperationInterface $operation, array $context = [])
    {
        $destination->{$propertyName} = $destination->{$propertyName} . $this->append;
    }
}