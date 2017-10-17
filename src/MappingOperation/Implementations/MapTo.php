<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;

/**
 * Class MapTo.
 *
 * Allows a property to be mapped itself.
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapTo extends DefaultMappingOperation
{
    /**
     * @var string
     */
    private $destinationClass;

    /**
     * @var AutoMapperInterface
     */
    private $mapper;

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
     * @param AutoMapperInterface $mapper
     */
    public function setMapper(AutoMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        $value = $this->getPropertyAccessor()->getProperty($source, $propertyName);

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
