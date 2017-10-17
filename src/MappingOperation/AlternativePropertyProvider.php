<?php

namespace AutoMapperPlus\MappingOperation;

/**
 * Interface AlternativePropertyProvider
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface AlternativePropertyProvider
{
    /**
     * @return string
     */
    public function getAlternativePropertyName(): string;
}
