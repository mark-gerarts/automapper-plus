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
    public const SOURCE_STACK_CONTEXT = '__source_stack';
    public const DESTINATION_STACK_CONTEXT = '__destination_stack';
    public const PROPERTY_STACK_CONTEXT = '__property_stack';
    public const DESTINATION_CONTEXT = '__destination';
    public const DESTINATION_CLASS_CONTEXT = '__destination_class';

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

    private function push($key, $value, &$context)
    {
        if (!array_key_exists($key, $context)) {
            $stack = [];
        } else {
            $stack = $context[$key];
        }
        $stack[] = $value;
        $context[$key] = $stack;
    }

    private function pop($key, &$context)
    {
        array_pop($context[$key]);
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
        } else {
            $sourceClass = \gettype($source);
            if ($sourceClass !== DataType::ARRAY) {
                throw UnsupportedSourceTypeException::fromType($sourceClass);
            }
        }

        $context[self::DESTINATION_CLASS_CONTEXT] = $destinationClass;

        $mapping = $this->getMapping($sourceClass, $destinationClass);
        if ($mapping->providesCustomMapper()) {
            return $this->getCustomMapper($mapping)->map($source, $destinationClass);
        }

        if ($mapping->hasCustomConstructor()) {
            $destinationObject = $mapping->getCustomConstructor()(
                $source,
                $this,
                $context
            );
        } elseif (interface_exists($destinationClass)) {
            // If we're mapping to an interface a valid custom constructor has
            // to be provided. Otherwise we can't know what to do.
            $message = 'Mapping to an interface is not possible. Please '
                . 'provide a concrete class or use mapToObject instead.';
            throw new AutoMapperPlusException($message);
        } else {
            $destinationObject = new $destinationClass;
        }

        $context[self::DESTINATION_CONTEXT] = $destinationObject;

        $this->push(self::SOURCE_STACK_CONTEXT, $source, $context);
        $this->push(self::DESTINATION_STACK_CONTEXT, $destinationObject, $context);

        try {
            return $this->doMap($source, $destinationObject, $mapping, $context);
        } finally {
            $this->pop(self::DESTINATION_STACK_CONTEXT, $context);
            $this->pop(self::SOURCE_STACK_CONTEXT, $context);
        }
    }

    /**
     * @inheritdoc
     */
    public function mapMultiple(
        $sourceCollection,
        string $destinationClass,
        array $context = []
    ): array
    {
        if (!is_iterable($sourceCollection)) {
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
        } else {
            $sourceClass = \gettype($source);
            if ($sourceClass !== DataType::ARRAY) {
                throw UnsupportedSourceTypeException::fromType($sourceClass);
            }
        }

        $destinationClass = \get_class($destination);

        $context[self::DESTINATION_CONTEXT] = $destination;
        $context[self::DESTINATION_CLASS_CONTEXT] = $destinationClass;

        $this->push(self::SOURCE_STACK_CONTEXT, $source, $context);
        $this->push(self::DESTINATION_STACK_CONTEXT, $destination, $context);
        try {
            $mapping = $this->getMapping($sourceClass, $destinationClass);
            if ($mapping->providesCustomMapper()) {
                return $this->getCustomMapper($mapping)->mapToObject(
                    $source,
                    $destination,
                    $context
                );
            }

            return $this->doMap(
                $source,
                $destination,
                $mapping,
                $context
            );
        } finally {
            $this->pop(self::DESTINATION_STACK_CONTEXT, $context);
            $this->pop(self::SOURCE_STACK_CONTEXT, $context);
        }
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
    )
    {
        $propertyNames = $mapping->getTargetProperties($destination, $source);
        foreach ($propertyNames as $propertyName) {
            $this->push(self::PROPERTY_STACK_CONTEXT, $propertyName, $context);
            try {
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
            } finally {
                $this->pop(self::PROPERTY_STACK_CONTEXT, $context);
            }
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
