<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;

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
     * @param string $targetPropertyName
     *   The name of a property of the destination class.
     * @param $operation
     *   The operation to be performed. Either a callback function or an
     *   instance of MappingOperationInterface. When a regular callback is
     *   given, it will be wrapped in a MapFrom operation for convenience.
     * @return MappingInterface
     *   Return this mapping to allow for chaining.
     */
    public function forMember(string $targetPropertyName, $operation): MappingInterface;

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
     * the options associated with this mapping.
     *
     * @param callable $configurator
     * @return MappingInterface
     */
    public function setDefaults(callable $configurator): MappingInterface;

    /**
     * @return Options
     */
    public function getOptions(): Options;

    /**
     * @return MappingInterface
     */
    public function skipConstructor(): MappingInterface;

    /**
     * @return MappingInterface
     */
    public function dontSkipConstructor(): MappingInterface;

    /**
     * @param NamingConventionInterface $sourceNamingConvention
     * @param NamingConventionInterface $destinationNamingConvention
     * @return MappingInterface
     */
    public function withNamingConventions(
        NamingConventionInterface $sourceNamingConvention,
        NamingConventionInterface $destinationNamingConvention
    ): MappingInterface;

    /**
     * @param MappingOperationInterface $mappingOperation
     * @return MappingInterface
     */
    public function withDefaultOperation(MappingOperationInterface $mappingOperation): MappingInterface;
}
