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
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\Middleware\MapperMiddleware;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Middleware\PropertyMiddleware;

/**
 * Class AutoMapper
 *
 * @package AutoMapperPlus
 */
class AutoMapper implements AutoMapperInterface
{
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

        $context = array_merge(
            [self::DESTINATION_CLASS_CONTEXT => $destinationClass],
            $context
        );
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

        $context[self::DESTINATION_CONTEXT] = $destinationObject;

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

        $context = array_merge(
            [
                self::DESTINATION_CONTEXT => $destination,
                self::DESTINATION_CLASS_CONTEXT => $destinationClass
            ],
            $context
        );

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
    }

    /**
     * Performs the actual transferring of properties, involving all matching mapper and property middleware.
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
        $mapperMiddlewares = $this->getMapperMiddlewares($source, $destination, $mapping, $context);
        foreach ($mapperMiddlewares[Middleware::BEFORE] as $middleware) {
            $middleware->map($source, $destination, $mapping, $context);
        }

        $overrideMiddlewares = $mapperMiddlewares[Middleware::OVERRIDE];
        if ($overrideMiddlewares) {
            foreach ($overrideMiddlewares as $middleware) {
                $middleware->map($source, $destination, $mapping, $context);
            }
        } else {
            $this->doMapDefault($source, $destination, $mapping, $context);
        }

        foreach ($mapperMiddlewares[Middleware::AFTER] as $middleware) {
            $middleware->map($source, $destination, $mapping, $context);
        }

        return $destination;
    }

    /**
     * Performs the actual default transferring of properties, involving all registered property middleware.
     *
     * @param $source
     * @param $destination
     * @param MappingInterface $mapping
     * @param array $context
     * @return mixed
     *   The destination object with mapped properties.
     */
    protected function doMapDefault(
        $source,
        $destination,
        MappingInterface $mapping,
        array $context = []
    )
    {
        $propertyNames = $mapping->getTargetProperties($destination, $source);
        foreach ($propertyNames as $propertyName) {
            $mappingOperation = $mapping->getMappingOperationFor($propertyName);

            if ($mappingOperation instanceof MapperAwareOperation) {
                $mappingOperation->setMapper($this);
            }
            if ($mappingOperation instanceof ContextAwareOperation) {
                $mappingOperation->setContext($context);
            }

            $propertyMiddlewares = $this->getPropertyMiddlewares($propertyName, $source, $destination, $mapping, $mappingOperation, $context);
            foreach ($propertyMiddlewares[Middleware::BEFORE] as $middleware) {
                $middleware->mapProperty($propertyName, $source, $destination, $mapping, $mappingOperation, $context);
            }

            $overrideMiddlewares = $propertyMiddlewares[Middleware::OVERRIDE];
            if ($overrideMiddlewares) {
                foreach ($overrideMiddlewares as $middleware) {
                    $middleware->mapProperty($propertyName, $source, $destination, $mapping, $mappingOperation, $context);
                }
            } else {
                $mappingOperation->mapProperty(
                    $propertyName,
                    $source,
                    $destination
                );
            }

            foreach ($propertyMiddlewares[Middleware::AFTER] as $middleware) {
                $middleware->mapProperty($propertyName, $source, $destination, $mapping, $mappingOperation, $context);
            }
        }
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

    /**
     * @param $source
     * @param $destination
     * @param string $propertyName
     * @param MappingInterface $mapping
     * @param MappingOperationInterface $mappingOperation
     * @param array $context
     * @return PropertyMiddleware[][]
     */
    private function getPropertyMiddlewares(
        $propertyName,
        $source,
        $destination,
        MappingInterface $mapping,
        MappingOperationInterface $mappingOperation,
        array $context = []): array
    {
        $propertyMiddleware = [Middleware::AFTER => [], Middleware::OVERRIDE => NULL, Middleware::BEFORE => []];
        foreach ($this->getConfiguration()->getPropertyMiddlewares() as $middleware) {
            $supports = intval($middleware->supportsMapProperty($propertyName, $source, $destination, $mapping, $mappingOperation, $context));
            if ($supports != Middleware::SKIP) {
                $propertyMiddleware[$supports][] = $middleware;
            }
        }
        return $propertyMiddleware;
    }

    /**
     * @param $source
     * @param $destination
     * @param MappingInterface $mapping
     * @param array $context
     * @return MapperMiddleware[][]
     */
    private function getMapperMiddlewares(
        $source,
        $destination,
        MappingInterface $mapping,
        array $context = []): array
    {
        $mapperMiddlewares = [Middleware::AFTER => [], Middleware::OVERRIDE => [], Middleware::BEFORE => []];
        foreach ($this->getConfiguration()->getMapperMiddlewares() as $middleware) {
            $supports = intval($middleware->supportsMap($source, $destination, $mapping, $context));
            if ($supports != Middleware::SKIP) {
                $mapperMiddlewares[$supports][] = $middleware;
            }
        }
        return $mapperMiddlewares;
    }
}
