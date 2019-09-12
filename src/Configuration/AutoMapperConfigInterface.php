<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\Middleware\DefaultMiddleware;
use AutoMapperPlus\Middleware\MapperMiddleware;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Middleware\PropertyMiddleware;

/**
 * Interface AutoMapperConfigInterface
 *
 * @package AutoMapperPlus\Configuration
 */
interface AutoMapperConfigInterface
{
    /**
     * Checks if a mapping exists between the given classes.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return bool
     */
    public function hasMappingFor(
        string $sourceClassName,
        string $destinationClassName
    ): bool;

    /**
     * Retrieves the mapping for the given classes.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return MappingInterface|null
     */
    public function getMappingFor(
        string $sourceClassName,
        string $destinationClassName
    ): ?MappingInterface;

    /**
     * Register a mapping between two classes. Without any additional
     * configuration, this will perform the default operation for every
     * property.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return MappingInterface
     */
    public function registerMapping(
        string $sourceClassName,
        string $destinationClassName
    ): MappingInterface;

    /**
     * Register middlewares after existing ones.
     *
     * All middlewares will be invoked in order.
     *
     * @param Middleware ...$middlewares
     * @return self
     *
     * @see DefaultMiddleware
     * @see PropertyMiddleware
     * @see MapperMiddleware
     */
    public function registerMiddlewares(Middleware ...$middlewares): AutoMapperConfigInterface;

    /**
     * @return Options
     */
    public function getOptions(): Options;

    /**
     * @return MapperMiddleware
     */
    public function getDefaultMapperMiddleware();

    /**
     * @return PropertyMiddleware
     */
    public function getDefaultPropertyMiddleware();

    /**
     * @return MapperMiddleware[]
     */
    public function getMapperMiddlewares();

    /**
     * @return PropertyMiddleware[]
     */
    public function getPropertyMiddlewares();
}
