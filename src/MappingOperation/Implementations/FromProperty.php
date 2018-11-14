<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\AlternativePropertyProvider;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Reversible;
use AutoMapperPlus\NameResolver\CallbackNameResolver;

/**
 * Class FromProperty
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class FromProperty extends DefaultMappingOperation implements
    AlternativePropertyProvider,
    Reversible,
    // We need to be mapper aware to be able to pass the mapper to a chained
    // operation.
    MapperAwareOperation
{
    /**
     * @var MappingOperationInterface|null
     */
    private $nextOperation;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * FromProperty constructor.
     *
     * @param string $propertyName
     */
    public function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @inheritdoc
     */
    public function getSourcePropertyName(string $propertyName): string
    {
        return $this->propertyName;
    }

    /**
     * @inheritdoc
     */
    public function getAlternativePropertyName(): string
    {
        return $this->propertyName;
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
    public function getReverseOperation(
        string $originalProperty,
        Options $options
    ): MappingOperationInterface {
        return new static($originalProperty);
    }

    /**
     * @inheritdoc
     */
    public function getReverseTargetPropertyName
    (
        string $originalProperty,
        Options $options
    ): string
    {
        return $this->propertyName;
    }

    /**
     * Chain a MapTo operation, making the MapTo use this operation's property
     * name instead.
     *
     * Note: because MapTo is not reversible, the MapTo part gets lost when
     * reversing the mapping.
     *
     * @todo: extend to other operations, or maybe a __call?
     *
     * @param string $class
     * @return FromProperty
     */
    public function mapTo(string $class): FromProperty
    {
        $this->nextOperation = new MapTo($class);
        return $this;
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
            return $this->propertyName;
        }));
        $this->nextOperation->setOptions($options);

        // The chained operation will now use the property name assigned to
        // FromProperty, so we can go ahead and call it.
        $this->nextOperation->mapProperty($propertyName, $source, $destination);
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
