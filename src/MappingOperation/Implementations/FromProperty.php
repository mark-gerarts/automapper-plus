<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\MappingOperation\AlternativePropertyProvider;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;

/**
 * Class FromProperty
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class FromProperty extends DefaultMappingOperation implements AlternativePropertyProvider
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
}
