<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\NameResolver\NameResolverInterface;
use AutoMapperPlus\PrivateAccessor\PrivateAccessor;

/**
 * Class GetProperty
 *
 * An operation that simply extracts the value of a property of the source
 * object. A custom name resolver can be provided.
 *
 * @package AutoMapperPlus\MappingOperation
 */
class GetProperty implements MappingOperationInterface
{
    /**
     * @var NameResolverInterface
     */
    private $nameResolver;

    /**
     * GetProperty constructor.
     *
     * @param NameResolverInterface $nameResolver
     */
    public function __construct(NameResolverInterface $nameResolver)
    {
        $this->nameResolver = $nameResolver;
    }

    /**
     * @inheritdoc
     */
    public function __invoke
    (
        $from,
        $to,
        string $propertyName,
        AutoMapperConfigInterface $config
    ): void
    {
        $fromReflectionClass = new \ReflectionClass($from);
        $toReflectionClass = new \ReflectionClass($to);
        $sourcePropertyName = $this->nameResolver->resolve($propertyName);
        if (!$fromReflectionClass->hasProperty($sourcePropertyName)) {
            // We could add a config option to throw an error here instead.
            return;
        }

        // Get the source value.
        $sourceProperty = $fromReflectionClass->getProperty($sourcePropertyName);
        $sourceValue = $sourceProperty->isPublic()
            ? $from->{$sourcePropertyName}
            : PrivateAccessor::getPrivate($from, $sourcePropertyName);

        // Set the value on the destination object.
        if ($toReflectionClass->getProperty($propertyName)->isPublic()) {
            $to->{$propertyName} = $sourceValue;
        }
        else {
            PrivateAccessor::setPrivate($to, $propertyName, $sourceValue);
        }
    }

}
