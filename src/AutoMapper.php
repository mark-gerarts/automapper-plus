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
    public const DESTINATION_CONTEXT = '__destination';

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
    public function map($source, $target, array $context = [])
    {
        if ($source === null) {
            return null;
        }

        $sourceClass = $this->getSourceClass($source);
        $targetClass = $this->getTargetClass($target);
        $context = array_merge($context, [self::DESTINATION_CONTEXT => $target]);

        $mapping = $this->getMapping($sourceClass, $targetClass);
        if ($mapping->providesCustomMapper()) {
            return $this->getCustomMapper($mapping)->map($source, $target, $context);
        }

        if (!is_object($target)) {
            $target = $mapping->hasCustomConstructor()
                ? $mapping->getCustomConstructor()($source, $this, $context)
                : new $targetClass;
        }

        return $this->doMap($source, $target, $mapping, $context);
    }

    /**
     * @param mixed $source The source data.
     * @param object $target An existing object.
     * @param array $context
     * @return mixed The mapped object.
     * @throws AutoMapperPlusException
     *
     * @deprecated The `map` method should now be used instead.
     */
    public function mapToObject($source, $target, array $context = [])
    {
        return $this->map($source, $target, $context);
    }

    /**
     * @param $source The source object or data.
     * @return string The source class name or data type.
     * @throws AutoMapperPlusException
     */
    private function getSourceClass($source): string
    {
        if (\is_object($source)) {
            return \get_class($source);
        }

        $sourceType= \gettype($source);
        if (DataType::isDataType($sourceType)) {
            return $sourceType;
        }

        $message = sprintf('Unsupported source type: %s', gettype($source));
        throw new AutoMapperPlusException($message);
    }

    /**
     * @param $target The target data or string.
     * @return string The target class name or data type.
     * @throws AutoMapperPlusException
     */
    private function getTargetClass($target): string
    {
        if (is_string($target)) {
            return $target;
        }
        if (is_object($target)) {
            return get_class($target);
        }
        if (is_array($target)) {
            return DataType::ARRAY;
        }

        $message = sprintf('Unsupported target type: %s', gettype($target));
        throw new AutoMapperPlusException($message);
    }

    /**
     * @inheritdoc
     */
    public function mapMultiple(
        $sourceCollection,
        string $targetClass,
        array $context = []
    ): array {
        $mappedResults = [];
        foreach ($sourceCollection as $source) {
            $mappedResults[] = $this->map($source, $targetClass, $context);
        }

        return $mappedResults;
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
    protected function getMapping(
        string $sourceClass,
        string $destinationClass
    ): MappingInterface {
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
