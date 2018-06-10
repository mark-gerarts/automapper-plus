<?php
/**
 * Created by PhpStorm.
 * User: slava
 * Date: 6/10/18
 * Time: 3:27 PM
 */

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