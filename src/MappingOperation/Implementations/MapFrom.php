<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareTrait;

/**
 * Class MapFrom
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapFrom extends DefaultMappingOperation implements MapperAwareOperation
{
    use MapperAwareTrait;

    /**
     * @var callable
     */
    protected $valueCallback;

    /**
     * MapFrom constructor.
     *
     * @param callable $valueCallback
     */
    public function __construct(callable $valueCallback)
    {
        $this->valueCallback = $valueCallback;
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        return ($this->valueCallback)($source, $this->mapper);
    }

    /**
     * @inheritdoc
     */
    protected function canMapProperty(string $propertyName, $source): bool
    {
        // Mapping with a callback is always possible, regardless of the source
        // properties.
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function setDestinationValue(
        $destination,
        string $propertyName,
        $value
    ): void {
        $this->propertyWriter->setProperty(
            $destination,
            $propertyName,
            $value
        );
    }
}
