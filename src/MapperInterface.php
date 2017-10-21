<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Exception\UnregisteredMappingException;

/**
 * Interface MapperInterface
 *
 * @package AutoMapperPlus
 */
interface MapperInterface
{
    /**
     * Maps an object to an instance of class $to, provided a mapping is
     * configured.
     *
     * @param $source
     *   The source object.
     * @param string $targetClass
     *   The target classname.
     * @return mixed
     *   An instance of class $to.
     * @throws UnregisteredMappingException
     */
    public function map($source, string $targetClass);

    /**
     * Maps properties of object $from to an existing object $to.
     *
     * @param $source
     *   The source object.
     * @param $destination
     *   The target object.
     * @return mixed
     *   $to, with properties copied from $from.
     * @throws UnregisteredMappingException
     */
    public function mapToObject($source, $destination);
}
