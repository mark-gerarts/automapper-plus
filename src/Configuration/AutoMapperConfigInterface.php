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
    public function hasConfigFor(string $from, string $to): bool;

    /**
     * @param string $from
     * @param string $to
     * @return MappingInterface|null
     */
    public function getConfigFor(string $from, string $to): ?MappingInterface;

    /**
     * @param string $from
     * @param string $to
     * @return void
     */
    public function registerMapping(string $from, string $to): void;
}
