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
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function hasMappingFor(string $from, string $to): bool;

    /**
     * @param string $from
     * @param string $to
     * @return MappingInterface|null
     */
    public function getMappingFor(string $from, string $to): ?MappingInterface;

    /**
     * @param string $from
     * @param string $to
     * @return MappingInterface
     */
    public function registerMapping(string $from, string $to): MappingInterface;
}
