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
     * @param array|\Traversable $sourceCollection
     *   The source collection containing objects.
     * @param string $targetClass
     *   The target classname.
     * @return array
     *   An array of mapped objects. Keys are not preserved.
     */
    public function mapMultiple($sourceCollection, string $targetClass): array;

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
