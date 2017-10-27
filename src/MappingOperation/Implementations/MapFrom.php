<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\MappingOperation\DefaultMappingOperation;

/**
 * Class MapFrom
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapFrom extends DefaultMappingOperation
{
    /**
     * @var callable
     */
    private $valueCallback;

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
        return ($this->valueCallback)($source);
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
}
