<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MappingOperation\Implementations\MapTo;
use function Functional\map;

/**
 * Class AutoMapper
 *
 * @package AutoMapperPlus
 */
class AutoMapper implements AutoMapperInterface
{
    /**
     * @var AutoMapperConfigInterface
     */
    private $autoMapperConfig;

    /**
     * AutoMapper constructor.
     *
     * @param AutoMapperConfigInterface $autoMapperConfig
     */
    function __construct(AutoMapperConfigInterface $autoMapperConfig = null)
    {
        $this->autoMapperConfig = $autoMapperConfig ?: new AutoMapperConfig();
    }

    /**
     * @inheritdoc
     */
    public static function initialize(callable $configurator): AutoMapperInterface
    {
        $mapper = new static;
        $configurator($mapper->autoMapperConfig);

        return $mapper;
    }

    /**
     * @inheritdoc
     */
    public function map($source, string $destinationClass)
    {
        if (is_null($source)) {
            return null;
        }

        $sourceClass = get_class($source);

        $mapping = $this->getMapping($sourceClass, $destinationClass);
        if ($mapping->providesCustomMapper()) {
            return $mapping->getCustomMapper()->map($source, $destinationClass);
        }

        $destinationObject = $mapping->hasCustomConstructor()
            ? $mapping->getCustomConstructor()($source)
            : new $destinationClass;

        return $this->doMap($source, $destinationObject, $mapping);
    }

    /**
     * @inheritdoc
     */
    public function mapMultiple($sourceCollection, string $destinationClass): array
    {
        return map($sourceCollection, function ($source) use ($destinationClass) {
            return $this->map($source, $destinationClass);
        });
    }

    /**
     * @inheritdoc
     */
    public function mapToObject($source, $destination)
    {
        $sourceClassName = get_class($source);
        $destinationClassName = get_class($destination);

        $mapping = $this->getMapping($sourceClassName, $destinationClassName);
        if ($mapping->providesCustomMapper()) {
            return $mapping->getCustomMapper()->mapToObject($source, $destination);
        }

        return $this->doMap($source, $destination, $mapping);
    }

    /**
     * Performs the actual transferring of properties.
     *
     * @param $source
     * @param $destination
     * @param MappingInterface $mapping
     * @return mixed
     *   The destination object with mapped properties.
     */
    protected function doMap($source, $destination, MappingInterface $mapping)
    {
        $propertyNames = $mapping->getTargetProperties($destination, $source);
        foreach ($propertyNames as $propertyName) {
            $mappingOperation = $mapping->getMappingOperationFor($propertyName);

            // @todo: find another solution to this hacky implementation of
            // recursive mapping.
            if ($mappingOperation instanceof MapTo) {
                $mappingOperation->setMapper($this);
            }

            $mappingOperation->mapProperty(
                $propertyName,
                $source,
                $destination
            );
        }

        return $destination;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(): AutoMapperConfigInterface
    {
        return $this->autoMapperConfig;
    }

    /**
     * @param string $sourceClass
     * @param string $destinationClass
     * @return MappingInterface
     * @throws UnregisteredMappingException
     */
    protected function getMapping
    (
        string $sourceClass,
        string $destinationClass
    ): MappingInterface
    {
        $mapping = $this->autoMapperConfig->getMappingFor(
            $sourceClass,
            $destinationClass
        );
        if ($mapping) {
            return $mapping;
        }

        throw UnregisteredMappingException::fromClasses(
            $sourceClass,
            $destinationClass
        );
    }
}
