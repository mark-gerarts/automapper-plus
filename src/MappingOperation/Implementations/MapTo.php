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
     * MapTo constructor.
     *
     * @param string $destinationClass
     */
    public function __construct(string $destinationClass)
    {
        $this->destinationClass = $destinationClass;
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
        $value = $this->getPropertyAccessor()->getProperty(
            $source,
            $this->getSourcePropertyName($propertyName)
        );

        return $this->isCollection($value)
            ? $this->mapper->mapMultiple($value, $this->destinationClass)
            : $this->mapper->map($value, $this->destinationClass);
    }

    /**
     * Checks if the provided input is a collection.
     * @todo: might want to move this outside of this class.
     *
     * @param $variable
     * @return bool
     */
    private function isCollection($variable): bool
    {
        return is_array($variable) || $variable instanceof \Traversable;
    }
}
