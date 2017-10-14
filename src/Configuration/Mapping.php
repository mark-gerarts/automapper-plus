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
     * @var array
     */
    private $options = [];

    /**
     * @var AutoMapperConfigInterface
     */
    private $config;

    /**
     * Mapping constructor.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @param AutoMapperConfigInterface $config
     * @param array $options
     *   Accepts the following keys:
     *   - defaultOperation
     *   - skipConstructor
     */
    public function __construct
    (
        string $sourceClassName,
        string $destinationClassName,
        AutoMapperConfigInterface $config,
        array $options
    )
    {
        $this->sourceClassName = $sourceClassName;
        $this->destinationClassName = $destinationClassName;
        $this->config = $config;
        $this->options = $options;
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
        callable $mapCallback
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
        if (!$mapCallback instanceof MappingOperationInterface) {
            $mapCallback = Operation::mapFrom($mapCallback);
        }
        $this->mappingOperations[$propertyName] = $mapCallback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reverseMap(array $options = []): MappingInterface
    {
        return $this->config->registerMapping(
            $this->getDestinationClassName(),
            $this->getSourceClassName(),
            $options
        );
    }

    /**
     * @inheritdoc
     */
    public function getMappingCallbackFor(string $propertyName): callable
    {
        return $this->mappingOperations[$propertyName] ?? $this->getDefaultOperation();
    }

    /**
     * @inheritdoc
     */
    public function shouldSkipConstructor(): bool
    {
        return (bool) $this->options['skipConstructor'] ?? false;
    }

    /**
     * @return MappingOperationInterface
     */
    protected function getDefaultOperation(): MappingOperationInterface
    {
        return $this->options['defaultOperation']
            ?? $this->config->getDefaultOperation();
    }
}
