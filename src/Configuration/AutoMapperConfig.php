<?php

namespace AutoMapperPlus\Configuration;

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
    private $configs = [];

    /**
     * @inheritdoc
     */
    public function hasConfigFor(string $from, string $to): bool
    {
        return !empty($this->getConfigFor($from, $to));
    }

    /**
     * @inheritdoc
     */
    public function getConfigFor(string $from, string $to): ?MappingInterface
    {
        return first($this->configs, function (MappingInterface $mapping) use ($from, $to){
            return $mapping->getFrom() == $from && $mapping->getTo() == $to;
        });
    }

    /**
     * @inheritdoc
     */
    public function registerMapping(string $from, string $to): void
    {
        $this->configs[] = new Mapping($from, $to);
    }
}
