<?php

namespace AutoMapperPlus\NameResolver\NamingConvention;

/**
 * Interface NamingConventionInterface
 *
 * @package AutoMapperPlus\NameResolver\NamingConvention
 */
interface NamingConventionInterface
{
    /**
     * @param string $name
     * @return string[]
     */
    public function toParts(string $name): array;

    /**
     * @param string[] $parts
     * @return string
     */
    public function fromParts(array $parts): string;
}
