<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\PropertyAccessor\PropertyAccessorInterface;

/**
 * Class DefaultMappingOperation
 *
 * @package AutoMapperPlus\MappingOperation
 */
class DefaultMappingOperation implements MappingOperationInterface
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @inheritdoc
     */
    public function mapProperty(string $propertyName, $source, $destination): void
    {
        if (!$this->canMapProperty($propertyName, $source)) {
            // Alternatively throw an error here.
            return;
        }
        $sourceValue = $this->getSourceValue($source, $propertyName);
        $this->setDestinationValue($destination, $propertyName, $sourceValue);
    }

    /**
     * @inheritdoc
     */
    public function setOptions(Options $options): void
    {
        $this->options = $options;
    }

    /**
     * @param string $propertyName
     * @param $source
     * @return bool
     */
    protected function canMapProperty(string $propertyName, $source): bool
    {

        $sourceReflectionClass = new \ReflectionClass($source);

        // @todo: convert name if needed.
        return $sourceReflectionClass->hasProperty($propertyName);
    }

    /**
     * @param $source
     * @param string $propertyName
     * @return mixed
     */
    protected function getSourceValue($source, string $propertyName)
    {
        // @todo: convert name if needed.
        return $this->getPropertyAccessor()->getProperty($source, $propertyName);
    }

    /**
     * @param $destination
     * @param string $propertyName
     * @param $value
     */
    protected function setDestinationValue($destination, string $propertyName, $value): void
    {
        $this->getPropertyAccessor()->setProperty($destination, $propertyName, $value);
    }

    /**
     * @return PropertyAccessorInterface
     */
    protected function getPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->options->getPropertyAccessor();
    }
}
