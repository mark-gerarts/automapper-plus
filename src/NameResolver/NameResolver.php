<?php

namespace AutoMapperPlus\NameResolver;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\NameConverter\NameConverter;

/**
 * Class NameResolver
 *
 * @package AutoMapperPlus\NameResolver
 */
class NameResolver implements NameResolverInterface
{
    public function getSourcePropertyName
    (
        string $targetPropertyName,
        $source,
        Options $options
    ): string
    {
        if (!$options->shouldConvertName()) {
            return $targetPropertyName;
        }

        return NameConverter::convert(
            $options->getDestinationMemberNamingConvention(),
            $options->getSourceMemberNamingConvention(),
            $targetPropertyName
        );
    }
}
