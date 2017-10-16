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

        return $this->mapper->map($value, $this->destinationClass);
    }
}
