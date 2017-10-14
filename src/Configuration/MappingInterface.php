<?php

namespace AutoMapperPlus\Configuration;

/**
 * Interface MappingInterface
 *
 * @package AutoMapperPlus\Configuration
 */
interface MappingInterface
{
    /**
     * @return string
     *   The source class.
     */
    public function getFrom(): string;

    /**
     * @return string
     *   The destination class.
     */
    public function getTo(): string;

    /**
     * Register an operation to be performed for the given property.
     *
     * @param string $propertyName
     *   The name of a property of the destination class.
     * @param callable $mapCallback
     *   The operation to be performed. If the callback does not implement the
     *   MappingOperationInterface, it willed be wrapped in the MapFrom
     *   operation.
     * @return MappingInterface
     *   Return this mapping to allow for chaining.
     */
    public function forMember(string $propertyName, callable $mapCallback): MappingInterface;

    /**
     * @param string $propertyName
     * @return callable
     */
    public function getMappingCallbackFor(string $propertyName): callable;

    /**
     * Whether or not the constructor will be skipped when a new object is
     * instantiated within this mapping.
     *
     * @return bool
     */
    public function shouldSkipConstructor(): bool;
}
