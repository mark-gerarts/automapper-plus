<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
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
     *   The target class.
     * @return mixed
     *   An instance on class $to.
     * @throws UnregisteredMappingException
     */
    public function map($from, string $to);

    /**
     * @param array|\Traversable $from
     *   The source collection.
     * @param string $to
     *   The target class
     * @return array
     *   An array of mapped objects. Keys are not preserved.
     */
    public function mapMultiple($from, string $to): array;

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
     * will receive an AutoMapperConfig object as parameter. This acts as an
     * Alternative for .NET's Mapper.Initialize(cfg => {...});
     *
     * Usage:
     *   $mapper = AutoMapper::initialize(function ($config) {
     *       $config->registerMapping(...);
     *   });
     *
     * @param callable $configurator
     * @return AutoMapperInterface
     */
    public static function initialize(callable $configurator): AutoMapperInterface;

    /**
     * Returns the configuration object for the mapper.
     *
     * @return AutoMapperConfigInterface
     */
    public function getConfiguration(): AutoMapperConfigInterface;
}
