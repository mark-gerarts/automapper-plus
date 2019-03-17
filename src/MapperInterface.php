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
     * @param string|object $target
     *   When passed a classname (or 'array'), it will map to a new instance of
     *   this type. When passed an object, the mapping will be applied to this
     *   object.
     * @param array $context
     *   An arbitrary array of values that will be passed to supporting
     *   mapping operations (e.g. MapFrom) to alter their behaviour based on
     *   the context.
     *   This is not explicitly required on the interface yet to preserve
     *   backwards compatibility, but will be added in version 2.0.
     * @return mixed
     *   The mapped object.
     * @throws UnregisteredMappingException
     */
    public function map($source, $target, array $context = []);
}
