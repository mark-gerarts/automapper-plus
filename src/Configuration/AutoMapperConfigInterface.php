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
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function hasMappingFor(string $from, string $to): bool;

    /**
     * Retrieves the mapping for the given classes.
     *
     * @param string $from
     * @param string $to
     * @return MappingInterface|null
     */
    public function getMappingFor(string $from, string $to): ?MappingInterface;

    /**
     * Register a mapping between two classes. Without any additional
     * configuration, this will perform the default operation for every
     * property.
     *
     * @param string $from
     * @param string $to
     * @return MappingInterface
     */
    public function registerMapping(string $from, string $to): MappingInterface;
}
