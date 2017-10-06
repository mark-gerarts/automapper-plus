<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
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
        if (!$this->autoMapperConfig->hasConfigFor($fromReflectionClass->getName(), $to)) {
            throw UnregisteredMappingException::fromClasses(
                $fromReflectionClass->getName(),
                $to
            );
        }

        $mappingConfiguration = $this->autoMapperConfig->getConfigFor(
            $fromReflectionClass->getName(),
            $to
        );

        $mappedObject = $toReflectionClass->newInstanceWithoutConstructor();
        foreach ($toReflectionClass->getProperties() as $destinationProperty) {
            // @todo:
            // Delegate a lot of this logic onto the operation class.
            $destinationPropertyName = $destinationProperty->getName();
            $mappingCallback = $mappingConfiguration->getMappingCallbackFor($destinationPropertyName);
            if ($mappingCallback) {
                $mappedObject->{$destinationPropertyName} = $mappingCallback($from, $destinationPropertyName);
                continue;
            }

            if ($fromReflectionClass->getProperty($destinationPropertyName)->isPublic()) {
                $mappedObject->{$destinationPropertyName} = $from->{$destinationPropertyName};
                continue;
            }

            $privateAccessor = new PrivateAccessor();
            $value = $privateAccessor->getPrivate($from, $destinationPropertyName);
            $mappedObject->{$destinationPropertyName} = $value;
        }

        return $mappedObject;
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
}
