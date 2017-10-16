<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\MappingOperationInterface;
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
     * @var Options
     */
    private $options;

    /**
     * AutoMapperConfig constructor.
     *
     * @param callable $configurator
     */
    function __construct(callable $configurator = null)
    {
        $defaultConfig = Options::default();
        $this->options = $configurator
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

    /**
     * @inheritdoc
     */
    public function getOptions(): Options
    {
        return $this->options;
    }
}
