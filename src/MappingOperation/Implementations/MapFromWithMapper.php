<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareTrait;

/**
 * Class MapFromWithMapper
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapFromWithMapper extends MapFrom implements MapperAwareOperation
{
    use MapperAwareTrait;

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        return ($this->valueCallback)($source, $this->mapper);
    }
}
