<?php

namespace AutoMapperPlus\Configuration;

/**
 * Interface AutoMapperConfigInterface
 *
 * @package AutoMapperPlus\Configuration
 */
interface AutoMapperConfigInterface
{
    /**
     * Checks if a mapping exists between the given classes.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return bool
     */
    public function hasMappingFor(
        string $sourceClassName,
        string $destinationClassName
    ): bool;

    /**
     * Retrieves the mapping for the given classes.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return MappingInterface|null
     */
    public function getMappingFor(
        string $sourceClassName,
        string $destinationClassName
    ): ?MappingInterface;

    /**
     * Register a mapping between two classes. Without any additional
     * configuration, this will perform the default operation for every
     * property.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return MappingInterface
     */
    public function registerMapping(
        string $sourceClassName,
        string $destinationClassName
    ): MappingInterface;

    /**
     * @return Options
     */
    public function getOptions(): Options;
}
