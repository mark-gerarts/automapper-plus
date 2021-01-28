<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\MappingOperation\AlternativePropertyProvider;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\NameResolver\CallbackNameResolver;

/**
 * Class FromMethod
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class FromMethod extends DefaultMappingOperation implements
    AlternativePropertyProvider,
    MapperAwareOperation
{
    /**
     * @var MappingOperationInterface|null
     */
    private $nextOperation;

    /**
     * @var string
     */
    private $methodName;

    /**
     * FromMethod constructor.
     * @param string $methodName
     */
    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * @inheritdoc
     */
    public function mapProperty(string $propertyName, $source, $destination): void {
        if ($this->nextOperation === null) {
            parent::mapProperty($propertyName, $source, $destination);
            return;
        }

        $this->mapPropertyWithNextOperation(
            $propertyName,
            $source,
            $destination
        );
    }

    /**
     * @inheritdoc
     */
    protected function canMapProperty(string $propertyName, $source): bool
    {
        $sourcePropertyName = $this->getSourcePropertyName($propertyName);

        return $this->getMethodReader()->hasMethod($source, $sourcePropertyName);
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        return $this->getMethodReader()->getMethod(
            $source,
            $this->getSourcePropertyName($propertyName)
        );
    }

    /**
     * @param string $propertyName
     * @param $source
     * @param $destination
     */
    protected function mapPropertyWithNextOperation(
        string $propertyName,
        $source,
        $destination
    ): void {
        // We have to make the overridden property available to the next
        // operation. To do this, we create a "one-time use" name resolver
        // to pass to the operation.
        $options = clone $this->options;
        $options->setNameResolver(new CallbackNameResolver(function () {
            return $this->methodName;
        }));
        $this->nextOperation->setOptions($options);

        // The chained operation will now use the property name assigned to
        // FromProperty, so we can go ahead and call it.
        $this->nextOperation->mapProperty($propertyName, $source, $destination);
    }

    /**
     * @inheritdoc
     */
    public function getSourcePropertyName(string $propertyName): string
    {
        return $this->methodName;
    }

    /**
     * @inheritdoc
     */
    public function getAlternativePropertyName(): string
    {
        return $this->methodName;
    }

    /**
     * @inheritdoc
     */
    public function setMapper(AutoMapperInterface $mapper): void
    {
        if ($this->nextOperation instanceof MapperAwareOperation) {
            $this->nextOperation->setMapper($mapper);
        }
    }
}
