<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\NameResolver\NameResolverInterface;
use AutoMapperPlus\PropertyAccessor\PropertyReaderInterface;
use AutoMapperPlus\PropertyAccessor\PropertyWriterInterface;

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
     * Options that are used will be stored for performance reasons, because
     * each property being mapped incurs a method call.
     */

    /**
     * @var NameResolverInterface
     */
    protected $nameResolver;

    /**
     * @var PropertyReaderInterface
     */
    protected $propertyReader;

    /**
     * @var PropertyWriterInterface
     */
    protected $propertyWriter;

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
        $this->nameResolver = $options->getNameResolver();
        $this->propertyReader = $options->getPropertyReader();
        $this->propertyWriter = $options->getPropertyWriter();
    }

    /**
     * @param string $propertyName
     * @param $source
     * @return bool
     */
    protected function canMapProperty(string $propertyName, $source): bool
    {
        $sourcePropertyName = $this->getSourcePropertyName($propertyName);

        return $this->getPropertyReader()->hasProperty($source, $sourcePropertyName);
    }

    /**
     * @param $source
     * @param string $propertyName
     * @return mixed
     */
    protected function getSourceValue($source, string $propertyName)
    {
        return $this->getPropertyReader()->getProperty(
            $source,
            $this->getSourcePropertyName($propertyName)
        );
    }

    /**
     * @param $destination
     * @param string $propertyName
     * @param $value
     */
    protected function setDestinationValue(
        $destination,
        string $propertyName,
        $value
    ): void {
        if ($value === null && $this->options->shouldIgnoreNullProperties()) {
            return;
        }

        $this->getPropertyWriter()->setProperty(
            $destination,
            $propertyName,
            $value
        );
    }

    /**
     * @return PropertyAccessorInterface
     * @deprecated The PropertyAccessorInterface has been split up in reading and writing. Use these instead.
     */
    protected function getPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->options->getPropertyAccessor();
    }

    /**
     * @return PropertyReaderInterface
     */
    protected function getPropertyReader(): PropertyReaderInterface
    {
        return $this->propertyReader;
    }

    /**
     * @return PropertyWriterInterface
     */
    protected function getPropertyWriter(): PropertyWriterInterface
    {
        return $this->propertyWriter;
    }

    /**
     * Returns the name of the property we should fetch from the source object.
     *
     * @param string $propertyName
     * @return string
     */
    protected function getSourcePropertyName(string $propertyName): string
    {
        return $this->nameResolver->getSourcePropertyName(
            $propertyName,
            $this,
            $this->options
        );
    }
}
