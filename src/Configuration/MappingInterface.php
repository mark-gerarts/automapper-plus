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
     */
    public function getFrom(): string;

    /**
     * @return string
     */
    public function getTo(): string;

    /**
     * @param string $propertyName
     * @param callable $mapCallback
     * @return MappingInterface
     */
    public function forMember(string $propertyName, callable $mapCallback): MappingInterface;

    /**
     * @param string $propertyName
     * @return callable|null
     */
    public function getMappingCallbackFor(string $propertyName): ?callable;
}
