<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\PrivateAccessor\PrivateAccessor;

/**
 * Class GetProperty
 *
 * An operation that simply extracts the value of a property of the source
 * object. A custom name resolver can be provided (@todo).
 *
 * @package AutoMapperPlus\MappingOperation
 */
class GetProperty implements MappingOperationInterface
{
    /**
     * @var \Closure
     */
    private $nameResolver;

    /**
     * GetProperty constructor.
     */
    public function __construct()
    {
        // @todo: replace this by an injectable NameResolver class.
        $this->nameResolver = function(string $targetProperty) {
            return $targetProperty;
        };
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
        $sourcePropertyName = ($this->nameResolver)($propertyName);
        $sourceProperty = $fromReflectionClass->getProperty($sourcePropertyName);
        if ($sourceProperty->isPublic()) {
            $to->{$propertyName} = $from->{$sourcePropertyName};
        }
        else {
            $to->{$propertyName} = PrivateAccessor::getPrivate($from, $sourcePropertyName);
        }
    }

}
