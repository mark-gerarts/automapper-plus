<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\NameResolver\IdentityNameResolver;
use AutoMapperPlus\NameResolver\NameConverterInterface;
use function Functional\first;

/**
 * Class AutoMapperConfig
 *
 * @package AutoMapperPlus\Configuration
 */
class AutoMapperConfig implements AutoMapperConfigInterface
{
    /**
     * @var MappingInterface[]
     */
    private $mappings = [];

    /**
     * @var Configuration
     */
    private $config;

    /**
     * AutoMapperConfig constructor.
     *
     * @param callable $configurator
     */
    function __construct(callable $configurator = null)
    {
        $defaultConfig = Configuration::default();
        $this->config = $configurator
            ? $configurator($defaultConfig)
            : $defaultConfig;
    }

    /**
     * @inheritdoc
     */
    public function hasMappingFor
    (
        string $sourceClassName,
        string $destinationClassName
    ): bool
    {
        return !empty($this->getMappingFor($sourceClassName, $destinationClassName));
    }

    /**
     * @inheritdoc
     */
    public function getMappingFor
    (
        string $sourceClassName,
        string $destinationClassName
    ): ?MappingInterface
    {
        return first(
            $this->mappings,
            function (MappingInterface $mapping) use ($sourceClassName, $destinationClassName) {
                return $mapping->getSourceClassName() == $sourceClassName
                    && $mapping->getDestinationClassName() == $destinationClassName;
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function registerMapping
    (
        string $sourceClassName,
        string $destinationClassName
    ): MappingInterface
    {
        $mapping = new Mapping(
            $sourceClassName,
            $destinationClassName,
            $this
        );
        $this->mappings[] = $mapping;

        return $mapping;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultOperation(): MappingOperationInterface
    {
        return $this->defaultOperation;
    }

    // setDefaults(callable $f)
}
