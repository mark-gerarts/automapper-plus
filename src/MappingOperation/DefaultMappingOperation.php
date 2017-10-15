<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Configuration;
use AutoMapperPlus\PropertyAccessor\PropertyAccessorInterface;

/**
 * Class DefaultMappingOperation
 *
 * @package AutoMapperPlus\MappingOperation
 */
class DefaultMappingOperation implements MappingOperationInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @inheritdoc
     */
    public function mapProperty(string $propertyName, $source, $destination): void
    {
        $sourceValue = $this->getSourceValue($source, $propertyName);
        $this->setDestinationValue($destination, $propertyName, $sourceValue);
    }

    /**
     * @inheritdoc
     */
    public function setConfig(Configuration $config): void
    {
        $this->config = $config;
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
        return $this->config->getPropertyAccessor();
    }
}
