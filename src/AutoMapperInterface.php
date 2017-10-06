<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Exception\UnregisteredMappingException;

/**
 * Interface AutoMapperInterface
 *
 * @package AutoMapperPlus
 */
interface AutoMapperInterface
{
    /**
     * Maps an object to an instance of class $to, provided a mapping is
     * configured.
     *
     * @param $from
     *   The source object.
     * @param string $to
     *   The target classname.
     * @return mixed
     *   An instance on class $to.
     * @throws UnregisteredMappingException
     */
    public function map($from, string $to);

    /**
     * Maps properties of object $from to an existing object $to.
     *
     * @param $from
     *   The source object.
     * @param $to
     *   The target object.
     * @return mixed
     *   $to, with properties copied from $from.
     * @throws UnregisteredMappingException
     */
    public function mapToObject($from, $to);

    /**
     * Instantiate the mapper with a given configuration callback. The callback
     * will receive an AutoMapperConfig object as parameter.
     *
     * Alternative for .NET's Mapper.Initialize(cfg => {...});
     *
     * @param callable $configurator
     * @return AutoMapperInterface
     */
    public static function initialize(callable $configurator): AutoMapperInterface;
}
