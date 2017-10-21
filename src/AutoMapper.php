<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
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
        $sourceClass = get_class($source);
        $mapping = $this->autoMapperConfig->getMappingFor($sourceClass, $destinationClass);
        $this->ensureConfigExists($sourceClass, $destinationClass);

        if ($mapping->providesCustomMapper()) {
            return $mapping->getCustomMapper()->map($source, $destinationClass);
        }

        // Check if we need to skip the constructor.
        if ($mapping->getOptions()->shouldSkipConstructor()) {
            $destinationReflectionClass = new \ReflectionClass($destinationClass);
            $destinationObject = $destinationReflectionClass->newInstanceWithoutConstructor();
        }
        else {
            $destinationObject = new $destinationClass;
        }

        return $this->mapToObject($source, $destinationObject);
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

        // First, check if a mapping exists for the given objects.
        $this->ensureConfigExists($sourceClassName, $destinationClassName);

        $mapping = $this->autoMapperConfig->getMappingFor(
            $sourceClassName,
            $destinationClassName
        );

        if ($mapping->providesCustomMapper()) {
            return $mapping->getCustomMapper()->mapToObject($source, $destination);
        }

        $destinationReflectionClass = new \ReflectionClass($destination);
        foreach ($destinationReflectionClass->getProperties() as $destinationProperty) {
            $mappingOperation = $mapping->getMappingOperationFor($destinationProperty->getName());

            // @todo: find another solution to this hacky implementation of
            // recursive mapping.
            if ($mappingOperation instanceof MapTo) {
                $mappingOperation->setMapper($this);
            }

            $mappingOperation->mapProperty(
                $destinationProperty->getName(),
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
     * @return void
     * @throws UnregisteredMappingException
     */
    protected function ensureConfigExists(string $sourceClass, string $destinationClass): void
    {
        $configExists = $this->autoMapperConfig->hasMappingFor($sourceClass, $destinationClass);
        if (!$configExists) {
            throw UnregisteredMappingException::fromClasses($sourceClass, $destinationClass);
        }
    }
}
