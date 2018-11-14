<?php

namespace AutoMapperPlus;

/**
 * Interface MapperAware
 *
 * @package AutoMapperPlus
 */
interface MapperAware
{
    /**
     * @param AutoMapperInterface $mapper
     */
    public function setMapper(AutoMapperInterface $mapper): void;
}
