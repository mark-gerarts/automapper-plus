<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\AlternativePropertyProvider;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Reversible;

/**
 * Class FromProperty
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class FromProperty extends DefaultMappingOperation implements
    AlternativePropertyProvider,
    Reversible
{
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
    public function getReverseOperation
    (
        string $originalProperty,
        Options $options
    ): MappingOperationInterface
    {
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
}
