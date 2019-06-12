<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\Exception\AutoMapperPlusException;
use AutoMapperPlus\Exception\InvalidArgumentException;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\Exception\UnsupportedSourceTypeException;
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
    public function map($source, string $destinationClass, array $context = [])
    {
        if ($source === null) {
            return null;
        }

        if (\is_object($source)) {
            $sourceClass = \get_class($source);
        }
        else {
            $sourceClass = \gettype($source);
            if ($sourceClass !== DataType::ARRAY) {
                throw UnsupportedSourceTypeException::fromType($sourceClass);
            }
        }

        $mapping = $this->getMapping($sourceClass, $destinationClass);
        if ($mapping->providesCustomMapper()) {
            return $this->getCustomMapper($mapping)->map($source, $destinationClass);
        }

        if ($mapping->hasCustomConstructor()) {
            $destinationObject = $mapping->getCustomConstructor()($source, $this, $context);
        }
        elseif (interface_exists($destinationClass)) {
            // If we're mapping to an interface a valid custom constructor has
            // to be provided. Otherwise we can't know what to do.
            $message = 'Mapping to an interface is not possible. Please '
                . 'provide a concrete class or use mapToObject instead.';
            throw new AutoMapperPlusException($message);
        }
        else {
            $destinationObject = new $destinationClass;
        }

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

        if(!is_iterable($sourceCollection)){
            throw new InvalidArgumentException(
                'The collection provided should be iterable.'
            );
        }

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
        if (\is_object($source)) {
            $sourceClass = \get_class($source);
        }
        else {
            $sourceClass = \gettype($source);
            if ($sourceClass !== DataType::ARRAY) {
                throw UnsupportedSourceTypeException::fromType($sourceClass);
            }
        }

        $destinationClass = \get_class($destination);

        $mapping = $this->getMapping($sourceClass, $destinationClass);
        if ($mapping->providesCustomMapper()) {
            return $this->getCustomMapper($mapping)->mapToObject($source, $destination, [
                self::DESTINATION_CONTEXT => $destination,
            ]);
        }

        return $this->doMap($source, $destination, $mapping, array_merge([
            self::DESTINATION_CONTEXT => $destination,
        ], $context));
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
