<?php

namespace AutoMapperPlus\NameResolver;

use AutoMapperPlus\Configuration\Options;

/**
 * Interface NameResolverInterface
 *
 * @package AutoMapperPlus\NameResolver
 */
interface NameResolverInterface
{
    public function getSourcePropertyName(
        string $targetPropertyName,
        $source,
        Options $options
    ): string;
}
