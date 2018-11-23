<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

/**
 * Interface AutoMapperInterface
 *
 * @package AutoMapperPlus
 */
interface AutoMapperInterface extends MapperInterface
{
    /**
     * @param array|\Traversable $sourceCollection
     *   The source collection containing objects.
     * @param string $targetClass
     *   The target classname.
     * @param array $context
     *   See MapperInterface::map()
     * @return array
     *   An array of mapped objects. Keys are not preserved.
     */
    public function mapMultiple(
        $sourceCollection,
        string $targetClass
        /**, array $context = [] */
    ): array;

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
