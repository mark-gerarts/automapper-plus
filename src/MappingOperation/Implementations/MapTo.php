<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareTrait;

/**
 * Class MapTo.
 *
 * Allows a property to be mapped itself.
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapTo extends DefaultMappingOperation implements MapperAwareOperation
{
    use MapperAwareTrait;

    /**
     * @var string
     */
    private $destinationClass;

    /**
     * @var bool
     */
    private $sourceIsObjectArray;

    /**
     * MapTo constructor.
     *
     * @param string $destinationClass
     * @param bool $sourceIsObjectArray
     *   Indicates whether or not an array as source value should be treated as
     *   a collection of elements, or as an array representing an object.
     */
    public function __construct(
        string $destinationClass,
        bool $sourceIsObjectArray = false
    ) {
        $this->destinationClass = $destinationClass;
        $this->sourceIsObjectArray = $sourceIsObjectArray;
    }

    /**
     * @return string
     */
    public function getDestinationClass(): string
    {
        return $this->destinationClass;
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        $value = $this->propertyReader->getProperty(
            $source,
            $this->getSourcePropertyName($propertyName)
        );

        return $this->sourceIsObjectArray || !$this->isCollection($value)
            ? $this->mapper->map($value, $this->destinationClass)
            :$this->mapper->mapMultiple($value, $this->destinationClass);
    }

    /**
     * @param $variable
     * @return bool
     */
    private function isCollection($variable): bool
    {
        return \is_array($variable) || $variable instanceof \Traversable;
    }
}
