<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\NameResolver\IdentityNameResolver;
use AutoMapperPlus\NameResolver\NameResolverInterface;
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
     * @var callable
     */
    private $defaultOperation;

    /**
     * @var NameResolverInterface
     */
    private $defaultNameResolver;

    /**
     * AutoMapperConfig constructor.
     *
     * @param NameResolverInterface|null $defaultNameResolver
     * @param callable|null $defaultOperation
     */
    function __construct
    (
        NameResolverInterface $defaultNameResolver = null,
        callable $defaultOperation = null
    )
    {
        $this->defaultNameResolver = $defaultNameResolver ?: new IdentityNameResolver();
        $this->defaultOperation = $defaultOperation ?: Operation::getProperty($this->defaultNameResolver);
    }

    /**
     * @inheritdoc
     */
    public function hasMappingFor(
        string $sourceClassName,
        string $destinationClassName
    ): bool
    {
        return !empty($this->getMappingFor($sourceClassName, $destinationClassName));
    }

    /**
     * @inheritdoc
     */
    public function getMappingFor(
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
    public function registerMapping(
        string $sourceClassName,
        string $destinationClassName,
        array $options = []
    ): MappingInterface
    {
        $mapping = new Mapping(
            $sourceClassName,
            $destinationClassName,
            $this->mergeWithDefaults($options)
        );
        $this->mappings[] = $mapping;

        return $mapping;
    }

    /**
     * @param array $mappingOptions
     * @return array
     */
    protected function mergeWithDefaults(array $mappingOptions): array
    {
        $defaults = [
            'skipConstructor' => false,
            'defaultOperation' => $this->defaultOperation
        ];

        return $mappingOptions + $defaults;
    }
}
