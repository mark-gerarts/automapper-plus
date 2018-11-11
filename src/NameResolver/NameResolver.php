<?php

namespace AutoMapperPlus\NameResolver;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\AlternativePropertyProvider;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\NameConverter\NameConverter;

/**
 * Class NameResolver
 *
 * @package AutoMapperPlus\NameResolver
 */
class NameResolver implements NameResolverInterface
{
    /**
     * @inheritdoc
     */
    public function getSourcePropertyName(
        string $targetPropertyName,
        MappingOperationInterface $operation,
        Options $options
    ): string {
        if ($operation instanceof AlternativePropertyProvider) {
            return $operation->getAlternativePropertyName();
        }

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
