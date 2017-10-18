<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;

/**
 * Interface Reversible
 *
 * A MappingOperation defining this interface will be able to be applied in the
 * reverse direction when `reverseMap()` is called.
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface Reversible
{
    /**
     * Returns what would be the reverse operation. This is used when applying
     * reverseMap().
     *
     * @param string $originalProperty
     *   The original property this operation was defined for.
     * @param Options $options
     *   The options of the mapping it was originally defined for.
     * @return MappingOperationInterface
     */
    public function getReverseOperation(
        string $originalProperty,
        Options $options
    ): MappingOperationInterface;

    /**
     * In order to be able to register the mapping in the reverse direction,
     * we have to know what the new target property is.
     *
     * @param string $originalProperty
     * @param Options $options
     * @return string
     */
    public function getReverseTargetPropertyName(
        string $originalProperty,
        Options $options
    ): string;
}
