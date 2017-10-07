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
    public function hasMappingFor(string $from, string $to): bool
    {
        return !empty($this->getMappingFor($from, $to));
    }

    /**
     * @inheritdoc
     */
    public function getMappingFor(string $from, string $to): ?MappingInterface
    {
        return first($this->mappings, function (MappingInterface $mapping) use ($from, $to) {
            return $mapping->getFrom() == $from && $mapping->getTo() == $to;
        });
    }

    /**
     * @inheritdoc
     */
    public function registerMapping(string $from, string $to): MappingInterface
    {
        $mapping = new Mapping($from, $to, $this->defaultOperation);
        $this->mappings[] = $mapping;

        return $mapping;
    }
}
