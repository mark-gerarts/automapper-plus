<?php

namespace AutoMapperPlus\MappingOperation;

/**
 * Interface ContextAwareOperation
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface ContextAwareOperation
{
    /**
     * Allows passing of a context array to the operation.
     *
     * @param array $context
     */
    public function setContext(array $context = []): void;
}
