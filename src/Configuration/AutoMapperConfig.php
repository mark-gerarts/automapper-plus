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
        $this->options = Options::default();
        if ($configurator) {
            $configurator($this->options);
        }
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
        $candidates = array_filter(
            $this->mappings,
            function (MappingInterface $mapping) use ($sourceClassName, $destinationClassName) {
                return is_a($sourceClassName, $mapping->getSourceClassName(), true)
                    && is_a($destinationClassName, $mapping->getDestinationClassName(), true);
            }
        );
        $specific = first(
            $candidates,
            function (MappingInterface $mapping) use ($sourceClassName, $destinationClassName) {
                return $mapping->getSourceClassName() == $sourceClassName
                    && $mapping->getDestinationClassName() == $destinationClassName;
            }
        );
        if ($this->options->isUseMappingOfParentClasses()) {
            return $specific ?? $this->getMostSpecificCandidate($candidates, $sourceClassName, $destinationClassName);
        } else {
            return $specific;
        }
    }

    protected function getMostSpecificCandidate(array $candidates, string $sourceClassName, string $destinationClassName)
    {
        $lowestDistance = PHP_INT_MAX;
        $selectedCandidate = null;
        /** @var MappingInterface $candidate */
        foreach($candidates as $candidate) {
            $distance = $this->getClassDistance($sourceClassName, $candidate->getSourceClassName())
                + $this->getClassDistance($destinationClassName, $candidate->getDestinationClassName());
            if ($distance < $lowestDistance) {
                $lowestDistance = $distance;
                $selectedCandidate = $candidate;
            }
        }
        return $selectedCandidate;
    }

    protected function getClassDistance($childClass, $parentClass)
    {
        if ($childClass === $parentClass) {
            return 0;
        }
        $result = 0;
        $childParents = class_parents($childClass, true);
        foreach($childParents as $childParent) {
            $result++;
            if ($childParent === $parentClass) {
                return $result;
            }
        }
        throw new \Exception("
            This error should have never be thrown.
            This could only happen, if given childClass is not a child of the given parentClass"
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
    public function getOptions(): Options
    {
        return $this->options;
    }
}
