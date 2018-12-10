<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Exception\AutoMapperPlusException;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MappingOperation\ContextAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;

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
    public function __construct(AutoMapperConfigInterface $autoMapperConfig = null)
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
    public function map($source, string $destinationClass, array $context = [])
    {
        if ($source === null) {
            return null;
        }

        if (\is_object($source)) {
            $sourceClass = \get_class($source);
        }
        else {
            $sourceClass= \gettype($source);
            if ($sourceClass !== DataType::ARRAY) {
                throw new AutoMapperPlusException(
                    'Mapping from something else than an object or array is not supported yet.'
                );
            }
        }

        $mapping = $this->getMapping($sourceClass, $destinationClass);
        if ($mapping->providesCustomMapper()) {
            return $this->getCustomMapper($mapping)->map($source, $destinationClass);
        }

        $destinationObject = $mapping->hasCustomConstructor()
            ? $mapping->getCustomConstructor()($source, $this, $context)
            : new $destinationClass;

        return $this->doMap($source, $destinationObject, $mapping, $context);
    }

    /**
     * @inheritdoc
     */
    public function mapMultiple(
        $sourceCollection,
        string $destinationClass,
        array $context = []
    ): array {
        $mappedResults = [];
        foreach ($sourceCollection as $source) {
            $mappedResults[] = $this->map($source, $destinationClass, $context);
        }

        return $mappedResults;
    }

    /**
     * @inheritdoc
     */
    public function mapToObject($source, $destination, array $context = [])
    {
        $sourceClassName = \get_class($source);
        $destinationClassName = \get_class($destination);

        $mapping = $this->getMapping($sourceClassName, $destinationClassName);
        if ($mapping->providesCustomMapper()) {
            return $this->getCustomMapper($mapping)->mapToObject($source, $destination);
        }

        return $this->doMap($source, $destination, $mapping, $context);
    }

    /**
     * Performs the actual transferring of properties.
     *
     * @param $source
     * @param $destination
     * @param MappingInterface $mapping
     * @param array $context
     * @return mixed
     *   The destination object with mapped properties.
     */
    protected function doMap(
        $source,
        $destination,
        MappingInterface $mapping,
        array $context = []
    ) {
        $propertyNames = $mapping->getTargetProperties($destination, $source);
        foreach ($propertyNames as $propertyName) {
            $mappingOperation = $mapping->getMappingOperationFor($propertyName);

            if ($mappingOperation instanceof MapperAwareOperation) {
                $mappingOperation->setMapper($this);
            }
            if ($mappingOperation instanceof ContextAwareOperation) {
                $mappingOperation->setContext($context);
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

    /**
     * @param MappingInterface $mapping
     *
     * @return MapperInterface|null
     */
    private function getCustomMapper(MappingInterface $mapping): ?MapperInterface
    {
        $customMapper = $mapping->getCustomMapper();

        if ($customMapper instanceof MapperAwareOperation) {
            $customMapper->setMapper($this);
        }

        return $customMapper;
    }
}
