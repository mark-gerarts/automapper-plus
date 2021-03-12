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
     * @template T of object
     * @param array|object $source
     *   The source object.
     * @param string $targetClass
     * @psalm-param class-string<T> $targetClass
     *   The target classname.
     * @param array $context
     *   An arbitrary array of values that will be passed to supporting
     *   mapping operations (e.g. MapFrom) to alter their behaviour based on
     *   the context.
     *   This is not explicitly required on the interface yet to preserve
     *   backwards compatibility, but will be added in version 2.0.
     * @return mixed
     * @psalm-return T
     *   An instance of class $to.
     * @throws UnregisteredMappingException
     */
    public function map($source, string $targetClass/**, array $context = [] */);

    /**
     * Maps properties of object $from to an existing object $to.
     *
     * @template T of object
     * @param array|object $source
     *   The source object.
     * @param object $destination
     * @psalm-param T $destination
     *   The target object.
     * @param array $context
     *   See MapperInterface::map()
     * @return mixed
     * @psalm-return T
     *   $to, with properties copied from $from.
     * @throws UnregisteredMappingException
     */
    public function mapToObject($source, $destination/**, array $context = [] */);
}
