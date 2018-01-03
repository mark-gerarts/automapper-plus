<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\Exception\NoConstructorSetException;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MapperInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;
use AutoMapperPlus\NameResolver\NameResolverInterface;

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
    public function forMember(
        string $targetPropertyName,
        $operation
    ): MappingInterface;

    /**
     * @param string $propertyName
     * @return MappingOperationInterface
     */
    public function getMappingOperationFor(string $propertyName): MappingOperationInterface;

    /**
     * Gets all operations that were explicitly defined for this mapping.
     *
     * @return MappingOperationInterface[]
     */
    public function getRegisteredMappingOperations(): array;

    /**
     * Creates a new mapping in the reverse direction.
     *
     * @return MappingInterface
     */
    public function reverseMap(): MappingInterface;

    /**
     * Copies another mapping. This means the config and explicitly defined
     * mapping operations will be copied. They can then be overridden for the
     * new mapping if needed.
     *
     * Uses the given source and destination classes to search for a defined
     * mapping to copy from. Use `copyFromMapping` to copy from a provided
     * mapping.
     *
     * @param string $sourceClass
     * @param string $destinationClass
     * @return MappingInterface
     * @throws UnregisteredMappingException
     */
    public function copyFrom(
        string $sourceClass,
        string $destinationClass
    ): MappingInterface;

    /**
     * @see MappingInterface::copyFrom().
     *
     * @param MappingInterface $mapping
     * @return MappingInterface
     */
    public function copyFromMapping(
        MappingInterface $mapping
    ): MappingInterface;

    /**
     * @return Options
     */
    public function getOptions(): Options;

    /**
     * Returns a list of properties on the target class that will have to be
     * mapped.
     * Requires both the source and the target object in case it is an object
     * crate.
     *
     * @param object $targetObject
     * @param object $sourceObject
     * @return string[]
     */
    public function getTargetProperties($targetObject, $sourceObject): array;

    /**
     * =========================================================================
     * The following methods are purely there for convenience, providing a way
     * to directly configure options.
     * So instead of using $mapping->getOptions()->changeSomeOption(), you can
     * call $mapping->changeSomeOption() directly.
     * =========================================================================
     */

    /**
     * Specifies a custom factory callback to instantiate the destination
     * object. This callback is given the source object as a parameter.
     *
     * @param callable $factoryCallback
     * @return MappingInterface
     */
    public function beConstructedUsing(
        callable $factoryCallback
    ): MappingInterface;

    /**
     * Retrieves the custom factory callback as set by beConstructedUsing().
     *
     * @return callable
     * @throws NoConstructorSetException
     */
    public function getCustomConstructor(): callable;

    /**
     * Whether or not a custom constructor callback has been provided using
     * beConstructedUsing().
     *
     * @return bool
     */
    public function hasCustomConstructor(): bool;

    /**
     * Allows overriding of the configuration. The $configurator will be passed
     * the options associated with this mapping.
     *
     * @param callable $configurator
     * @return MappingInterface
     */
    public function setDefaults(callable $configurator): MappingInterface;

    /**
     * Keep in mind that this will override any custom constructor callback set
     * use beConstructedUsing().
     *
     * @return MappingInterface
     */
    public function skipConstructor(): MappingInterface;

    /**
     * Keep in mind that this will override any custom constructor callback set
     * use beConstructedUsing().
     *
     * @return MappingInterface
     */
    public function dontSkipConstructor(): MappingInterface;

    /**
     * Specifies the naming conventions for this mapping.
     *
     * @param NamingConventionInterface $sourceNamingConvention
     * @param NamingConventionInterface $destinationNamingConvention
     * @return MappingInterface
     */
    public function withNamingConventions(
        NamingConventionInterface $sourceNamingConvention,
        NamingConventionInterface $destinationNamingConvention
    ): MappingInterface;

    /**
     * Specifies the default operation for this mapping.
     *
     * @param MappingOperationInterface $mappingOperation
     * @return MappingInterface
     */
    public function withDefaultOperation(MappingOperationInterface $mappingOperation): MappingInterface;

    /**
     * Specifies a name resolver to be used for this mapping.
     *
     * @param NameResolverInterface $nameResolver
     * @return MappingInterface
     */
    public function withNameResolver(NameResolverInterface $nameResolver): MappingInterface;

    /**
     * Registers a custom mapper to be used for this specific mapping.
     *
     * @param MapperInterface $mapper
     */
    public function useCustomMapper(MapperInterface $mapper): void;

    /**
     * @return bool
     */
    public function providesCustomMapper(): bool;

    /**
     * @return MapperInterface|null
     */
    public function getCustomMapper(): ?MapperInterface;
}
