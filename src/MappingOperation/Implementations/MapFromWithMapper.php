<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

/**
 * Class MapFromWithMapper
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 *
 * @deprecated `MapFrom` now has the same behaviour as `mapFromWithMapper`.
 *   Consider using it instead.
 */
class MapFromWithMapper extends MapFrom
{
    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        return ($this->valueCallback)($source, $this->mapper);
    }
}
