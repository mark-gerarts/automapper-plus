<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\Exception\InvalidPropertyException;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Operation;

/**
 * Class Mapping
 *
 * @package AutoMapperPlus\Configuration
 */
class Mapping implements MappingInterface
{
    /**
     * @var string
     */
    private $sourceClassName;

    /**
     * @var string
     */
    private $destinationClassName;

    /**
     * @var MappingOperationInterface[]
     */
    private $mappingOperations = [];

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var AutoMapperConfigInterface
     */
    private $autoMapperConfig;

    /**
     * Mapping constructor.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @param AutoMapperConfigInterface $autoMapperConfig
     */
    public function __construct
    (
        string $sourceClassName,
        string $destinationClassName,
        AutoMapperConfigInterface $autoMapperConfig
    )
    {
        $this->sourceClassName = $sourceClassName;
        $this->destinationClassName = $destinationClassName;
        $this->autoMapperConfig = $autoMapperConfig;
    }

    /**
     * @inheritdoc
     */
    public function getSourceClassName(): string
    {
        return $this->sourceClassName;
    }

    /**
     * @inheritdoc
     */
    public function getDestinationClassName(): string
    {
        return $this->destinationClassName;
    }

    /**
     * @inheritdoc
     */
    public function forMember
    (
        string $propertyName,
        $operation
    ): MappingInterface
    {
        // Ensure the property exists on the target class before registering it.
        if (!property_exists($this->getSourceClassName(), $propertyName)) {
            throw InvalidPropertyException::fromNameAndClass(
                $propertyName,
                $this->getSourceClassName()
            );
        }

        // If it's just a regular callback, wrap it in an operation.
        if (!$operation instanceof MappingOperationInterface) {
            $operation = Operation::mapFrom($operation);
        }

        // Make the config available to the operation.
        $operation->setConfig($this->config);

        $this->mappingOperations[$propertyName] = $operation;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reverseMap(array $options = []): MappingInterface
    {
        return $this->autoMapperConfig->registerMapping(
            $this->getDestinationClassName(),
            $this->getSourceClassName()
        );
    }

    /**
     * @inheritdoc
     */
    public function getMappingOperationFor(string $propertyName): MappingOperationInterface
    {
        return $this->mappingOperations[$propertyName]
            ?? $this->config->getDefaultMappingOperation();
    }

    /**
     * @inheritdoc
     */
    public function setDefaults(callable $configurator): MappingInterface
    {
        $this->config = clone $this->config;
        $configurator($this->config);

        return $this;
    }
}
