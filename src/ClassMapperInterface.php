<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Exception\UnregisteredMappingException;

/**
 * Interface ClassMapperInterface
 *
 * @package AutoMapperPlus
 */
interface ClassMapperInterface
{
    /**
     * Maps an object to an instance of class $to, provided a mapping is
     * configured.
     *
     * @param array|object $source
     *   The source object.
     * @param string $targetClass
     *   The target classname.
     * @param array $context
     *   An arbitrary array of values that will be passed to supporting
     *   mapping operations (e.g. MapFrom) to alter their behaviour based on
     *   the context.
     *   This is not explicitly required on the interface yet to preserve
     *   backwards compatibility, but will be added in version 2.0.
     * @return mixed
     *   An instance of class $to.
     * @throws UnregisteredMappingException
     */
    public function map($source, string $targetClass/**, array $context = [] */);
}
