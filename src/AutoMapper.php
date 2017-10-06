<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\PrivateAccessor\PrivateAccessor;

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
    public function map($from, string $to)
    {
        $fromReflectionClass = new \ReflectionClass($from);
        $toReflectionClass = new \ReflectionClass($to);
        $configExists = $this->autoMapperConfig->hasConfigFor($fromReflectionClass->getName(), $to);
        if (!$configExists) {
            throw UnregisteredMappingException::fromClasses(
                $fromReflectionClass->getName(),
                $to
            );
        }

        $mappingConfiguration = $this->autoMapperConfig->getConfigFor(
            $fromReflectionClass->getName(),
            $to
        );
        $targetObject = $toReflectionClass->newInstanceWithoutConstructor();

        return $this->transferProperties($from, $targetObject, $mappingConfiguration);
    }

    /**
     * @inheritdoc
     */
    public function mapToObject($from, $to)
    {
        $fromReflectionClass = new \ReflectionClass($from);
        $toReflectionClass = new \ReflectionClass($to);
        $configExists = $this->autoMapperConfig->hasConfigFor(
            $fromReflectionClass->getName(),
            $toReflectionClass->getName()
        );
        if (!$configExists) {
            throw UnregisteredMappingException::fromClasses(
                $fromReflectionClass->getName(),
                $toReflectionClass->getName()
            );
        }

        $mappingConfiguration = $this->autoMapperConfig->getConfigFor(
            $fromReflectionClass->getName(),
            $toReflectionClass->getName()
        );

        return $this->transferProperties($from, $to, $mappingConfiguration);
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
     * @param $sourceObject
     * @param $targetObject
     * @param MappingInterface $mapping
     * @return mixed
     */
    private function transferProperties
    (
        $sourceObject,
        $targetObject,
        MappingInterface $mapping
    )
    {
        $fromReflectionClass = new \ReflectionClass($sourceObject);
        $toReflectionClass = new \ReflectionClass($targetObject);

        foreach ($toReflectionClass->getProperties() as $destinationProperty) {
            // @todo:
            // Delegate a lot of this logic onto the operation class.
            $destinationPropertyName = $destinationProperty->getName();
            $mappingCallback = $mapping->getMappingCallbackFor($destinationPropertyName);
            if ($mappingCallback) {
                $targetObject->{$destinationPropertyName} = $mappingCallback($sourceObject, $destinationPropertyName);
                continue;
            }

            if ($fromReflectionClass->getProperty($destinationPropertyName)->isPublic()) {
                $targetObject->{$destinationPropertyName} = $sourceObject->{$destinationPropertyName};
                continue;
            }

            $privateAccessor = new PrivateAccessor();
            $value = $privateAccessor->getPrivate($sourceObject, $destinationPropertyName);
            $targetObject->{$destinationPropertyName} = $value;
        }

        return $targetObject;
    }
}
