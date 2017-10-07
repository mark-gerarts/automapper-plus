<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use function Functional\first;

/**
 * Class AutoMapperConfig
 *
 * @package AutoMapperPlus\Configuration
 */
class AutoMapperConfig implements AutoMapperConfigInterface
{
    /**
     * @var Mapping[]
     */
    private $mappings = [];

    /**
     * @var callable
     */
    private $defaultOperation;

    /**
     * AutoMapperConfig constructor.
     *
     * @param callable $defaultOperation
     */
    function __construct(callable $defaultOperation = null)
    {
        $this->defaultOperation = $defaultOperation ?: Operation::getProperty();
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
