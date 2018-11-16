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
    private $assumeCollection;

    /**
     * MapTo constructor.
     *
     * @param string $destinationClass
     * @param bool $assumeCollection
     *   Indicates whether or not an array as source value should be treated as
     *   a collection of elements, or as an array representing an object.
     */
    public function __construct(
        string $destinationClass,
        bool $assumeCollection = true
    ) {
        $this->destinationClass = $destinationClass;
        $this->assumeCollection = $assumeCollection;
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

        if (!$this->assumeCollection) {
            return $this->mapper->map($value, $this->destinationClass);
        }

        return $this->isCollection($value)
            ? $this->mapper->mapMultiple($value, $this->destinationClass)
            : $this->mapper->map($value, $this->destinationClass);
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
