<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\AutoMapperInterface;

/**
 * Interface MapperAwareOperation
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface MapperAwareOperation {
    /**
     * @param AutoMapperInterface $mapper
     */
    public function setMapper(AutoMapperInterface $mapper): void;
}
