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
     *   The source class.
     */
    private $from;

    /**
     * @var string
     *   The destination class.
     */
    private $to;

    /**
     * @var MappingOperationInterface[]
     */
    private $mappingOperations = [];

    /**
     * @var callable
     */
    private $defaultOperation;

    /**
     * Mapping constructor.
     *
     * @param string $from
     * @param string $to
     * @param callable $defaultOperation
     */
    public function __construct(string $from, string $to, callable $defaultOperation)
    {
        $this->from = $from;
        $this->to = $to;
        $this->defaultOperation = $defaultOperation;
    }

    /**
     * @inheritdoc
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @inheritdoc
     */
    public function getTo(): string
    {
        return $this->to;
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
        if (!property_exists($this->getTo(), $propertyName)) {
            throw InvalidPropertyException::fromNameAndClass($propertyName, $this->getTo());
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
    public function getMappingCallbackFor(string $propertyName): callable
    {
        return $this->mappingOperations[$propertyName] ?? $this->defaultOperation;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOperation(callable $defaultOperation): MappingInterface
    {
        $this->defaultOperation = $defaultOperation;

        return $this;
    }
}
