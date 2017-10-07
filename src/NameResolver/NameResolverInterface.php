<?php

namespace AutoMapperPlus\NameResolver;

/**
 * Interface NameResolverInterface
 *
 * @package AutoMapperPlus\NameResolver
 */
interface NameResolverInterface
{
    /**
     * When given the target property name, returns what the name of the source
     * property should be.
     *
     * @param string $name
     * @return string
     */
    public function resolve(string $name): string;
}
