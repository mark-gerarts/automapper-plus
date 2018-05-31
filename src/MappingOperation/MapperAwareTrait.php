<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\AutoMapperInterface;

/**
 * Trait MapperAwareTrait
 *
 * @package AutoMapperPlus\MappingOperation
 */
trait MapperAwareTrait
{
    /**
     * @var AutoMapperInterface
     */
    protected $mapper;

    /**
     * @param AutoMapperInterface $mapper
     */
    public function setMapper(AutoMapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }
}
