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
     * Maps an object to an instance of class $to, provided a mapping is
     * configured.
     *
     * @param $from
     * @param string $to
     * @return mixed
     */
    public function map($from, string $to);

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
