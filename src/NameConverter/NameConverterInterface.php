<?php

namespace AutoMapperPlus\NameConverter;

use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;

/**
 * Interface NameResolverInterface
 *
 * @package AutoMapperPlus\NameResolver
 */
interface NameConverterInterface
{
    /**
     * @param NamingConventionInterface $sourceNamingConvention
     * @param NamingConventionInterface $targetNamingConvention
     * @param string $source
     * @return string
     */
    public static function convert(
        NamingConventionInterface $sourceNamingConvention,
        NamingConventionInterface $targetNamingConvention,
        string $source
    ): string;
}
