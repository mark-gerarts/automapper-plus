<?php


namespace AutoMapperPlus\Test\Middleware;


use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\PropertyMiddleware;

class AnwserToUniverseMiddleware implements PropertyMiddleware
{
    public function supportsMapProperty($propertyName,
                                        $source,
                                        $destination,
                                        MappingInterface $mapping,
                                        MappingOperationInterface $operation,
                                        array $context = [])
    {
        return $propertyName == 'id';
    }

    public function mapProperty($propertyName,
                                $source,
                                $destination,
                                MappingInterface $mapping,
                                MappingOperationInterface $operation,
                                array $context = [])
    {
        $defaultValue = $mapping->getOptions()->getPropertyReader()->getProperty($destination, $propertyName);
        if ($defaultValue === NULL) {
            $mapping->getOptions()->getPropertyWriter()->setProperty($destination, $propertyName, 42);
        }
    }
}