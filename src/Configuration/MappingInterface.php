<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\MappingOperationInterface;

/**
 * Interface MappingInterface
 *
 * @package AutoMapperPlus\Configuration
 */
interface MappingInterface
{
    /**
     * @return string
     */
    public function getSourceClassName(): string;

    /**
     * @return string
     */
    public function getDestinationClassName(): string;

    /**
     * Register an operation to be performed for the given property.
     *
     * @param string $propertyName
     *   The name of a property of the destination class.
     * @param $operation
     *   The operation to be performed. Either a callback function or an
     *   instance of MappingOperationInterface. When a regular callback is
     *   given, it will be wrapped in a MapFrom operation for convenience.
     * @return MappingInterface
     *   Return this mapping to allow for chaining.
     */
    public function forMember(string $propertyName, $operation): MappingInterface;

    /**
     * @param string $propertyName
     * @return MappingOperationInterface
     */
    public function getMappingOperationFor(string $propertyName): MappingOperationInterface;

    /**
     * Creates a new mapping in the reverse direction.
     *
     * @return MappingInterface
     */
    public function reverseMap(): MappingInterface;

    /**
     * Allows overriding of the configuration. The $configurator will be passed
     * the config associated with this mapping.
     *
     * @param callable $configurator
     * @return MappingInterface
     */
    public function setDefaults(callable $configurator): MappingInterface;
}
