<?php

namespace AutoMapperPlus;

/**
 * Interface AutoMapperInterface
 *
 * @package AutoMapperPlus
 */
interface AutoMapperInterface
{
    /**
     * @param $from
     * @param string $to
     * @return mixed
     */
    public function map($from, string $to);
}
