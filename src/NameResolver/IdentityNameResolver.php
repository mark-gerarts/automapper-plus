<?php

namespace AutoMapperPlus\NameResolver;

/**
 * Class IdentityNameResolver
 *
 * @package AutoMapperPlus\NameResolver
 */
class IdentityNameResolver implements NameResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(string $name): string
    {
        return $name;
    }
}
