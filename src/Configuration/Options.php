<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MapperInterface;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;
use AutoMapperPlus\NameResolver\NameResolver;
use AutoMapperPlus\NameResolver\NameResolverInterface;
use AutoMapperPlus\PropertyAccessor\PropertyAccessor;
use AutoMapperPlus\PropertyAccessor\PropertyAccessorInterface;
use AutoMapperPlus\PropertyAccessor\PropertyReaderInterface;
use AutoMapperPlus\PropertyAccessor\PropertyWriterInterface;

/**
 * Class Options
 *
 * @package AutoMapperPlus\Configuration
 */
class Options
{
    /**
     * @var NamingConventionInterface|null
     */
    private $sourceMemberNamingConvention;

    /**
     * @var NamingConventionInterface|null
     */
    private $destinationMemberNamingConvention;

    /**
     * @var bool
     */
    private $shouldSkipConstructor = false;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var PropertyWriterInterface
     */
    private $propertyWriter;

    /**
     * @var PropertyReaderInterface
     */
    private $propertyReader;

    /**
     * @var NameResolverInterface
     */
    private $nameResolver;

    /**
     * @var MappingOperationInterface
     */
    private $defaultMappingOperation;

    /**
     * @var MapperInterface|null
     */
    private $customMapper;

    /**
     * @var bool
     */
    private $useSubstitution = true;

    /**
     * @var string[]
     */
    private $objectCrates = [];

    /**
     * @var bool
     */
    private $ignoreNullProperties = false;

    /**
     * @var bool
     */
    private $createUnregisteredMappings = false;

    /**
     * @return Options
     *
     * Note: the skipConstructor default will be replaced by dontSkipConstructor
     *       in the next major release.
     */
    public static function default(): Options
    {
        $options = new static;
        $options->skipConstructor();
        $options->setPropertyAccessor(new PropertyAccessor());
        $options->setDefaultMappingOperation(new DefaultMappingOperation());
        $options->setNameResolver(new NameResolver());
        $options->registerObjectCrate(\stdClass::class);

        return $options;
    }

    /**
     * @return NamingConventionInterface|null
     */
    public function getSourceMemberNamingConvention(): ?NamingConventionInterface
    {
        return $this->sourceMemberNamingConvention;
    }

    /**
     * @param NamingConventionInterface $sourceMemberNamingConvention
     */
    public function setSourceMemberNamingConvention(NamingConventionInterface $sourceMemberNamingConvention): void
    {
        $this->sourceMemberNamingConvention = $sourceMemberNamingConvention;
    }

    /**
     * @return NamingConventionInterface|null
     */
    public function getDestinationMemberNamingConvention(): ?NamingConventionInterface
    {
        return $this->destinationMemberNamingConvention;
    }

    /**
     * @param NamingConventionInterface $destinationMemberNamingConvention
     */
    public function setDestinationMemberNamingConvention(NamingConventionInterface $destinationMemberNamingConvention): void
    {
        $this->destinationMemberNamingConvention = $destinationMemberNamingConvention;
    }

    /**
     * @return bool
     */
    public function shouldSkipConstructor(): bool
    {
        return $this->shouldSkipConstructor;
    }

    public function skipConstructor(): void
    {
        $this->shouldSkipConstructor = true;
    }

    public function dontSkipConstructor(): void
    {
        $this->shouldSkipConstructor = false;
    }

    /**
     * @param bool $shouldSkipConstructor
     */
    public function setShouldSkipConstructor(bool $shouldSkipConstructor): void
    {
        $this->shouldSkipConstructor = $shouldSkipConstructor;
    }

    /**
     * @return PropertyAccessorInterface
     */
    public function getPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->propertyAccessor;
    }

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor): void
    {
        $this->propertyReader = $propertyAccessor;
        $this->propertyWriter = $propertyAccessor;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @return PropertyWriterInterface
     */
    public function getPropertyWriter(): PropertyWriterInterface
    {
        return $this->propertyWriter ?: $this->propertyAccessor;
    }

    /**
     * @param PropertyWriterInterface $propertyWriter
     */
    public function setPropertyWriter(PropertyWriterInterface $propertyWriter): void
    {
        $this->propertyWriter = $propertyWriter;
    }

    /**
     * @return PropertyReaderInterface
     */
    public function getPropertyReader(): PropertyReaderInterface
    {
        return $this->propertyReader ?: $this->propertyAccessor;
    }

    /**
     * @param PropertyReaderInterface $propertyReader
     */
    public function setPropertyReader(PropertyReaderInterface $propertyReader): void
    {
        $this->propertyReader = $propertyReader;
    }

    /**
     * @return MappingOperationInterface
     */
    public function getDefaultMappingOperation(): MappingOperationInterface
    {
        return $this->defaultMappingOperation;
    }

    /**
     * @param MappingOperationInterface $defaultMappingOperation
     */
    public function setDefaultMappingOperation(MappingOperationInterface $defaultMappingOperation): void
    {
        $this->defaultMappingOperation = $defaultMappingOperation;
    }

    /**
     * @return NameResolverInterface
     */
    public function getNameResolver(): NameResolverInterface
    {
        return $this->nameResolver;
    }

    /**
     * @param NameResolverInterface $nameResolver
     */
    public function setNameResolver(NameResolverInterface $nameResolver): void
    {
        $this->nameResolver = $nameResolver;
    }

    /**
     * Whether or not property names should be converted between source and
     * destination.
     *
     * @return bool
     */
    public function shouldConvertName(): bool
    {
        return $this->sourceMemberNamingConvention !== null
            && $this->destinationMemberNamingConvention !== null;
    }

    /**
     * @return MapperInterface|null
     */
    public function getCustomMapper(): ?MapperInterface
    {
        return $this->customMapper;
    }

    /**
     * @param MapperInterface $customMapper
     */
    public function setCustomMapper(MapperInterface $customMapper): void
    {
        $this->customMapper = $customMapper;
    }

    /**
     * @return bool
     */
    public function providesCustomMapper(): bool
    {
        return $this->customMapper !== null;
    }

    /**
     * @param string $className
     */
    public function registerObjectCrate(string $className): void
    {
        $this->objectCrates[$className] = true;
    }

    /**
     * @param string $className
     */
    public function removeObjectCrate(string $className): void
    {
        unset($this->objectCrates[$className]);
    }

    /**
     * @param string $className
     * @return bool
     */
    public function isObjectCrate(string $className): bool
    {
        return isset($this->objectCrates[$className]);
    }

    /**
     * @return bool
     */
    public function shouldUseSubstitution(): bool
    {
        return $this->useSubstitution;
    }

    public function allowSubstitution(): void
    {
        $this->useSubstitution = true;
    }

    public function disallowSubstitution(): void
    {
        $this->useSubstitution = false;
    }

    /**
     * If a source property is NULL, don't map it to the destination.
     */
    public function ignoreNullProperties(): void
    {
        $this->ignoreNullProperties = true;
    }

    /**
     * If a source property is NULL, map it to the destination.
     */
    public function dontIgnoreNullProperties(): void
    {
        $this->ignoreNullProperties = false;
    }

    public function shouldIgnoreNullProperties(): bool
    {
        return $this->ignoreNullProperties;
    }

    public function createUnregisteredMappings(): void
    {
        $this->createUnregisteredMappings = true;
    }

    public function dontCreateUnregisteredMappings(): void
    {
        $this->createUnregisteredMappings = false;
    }

    /**
     * Whether or not a mapping should be generated on the fly when trying to
     * execute an unknown mapping. If not, an exception is thrown instead.
     *
     * @return bool
     */
    public function shouldCreateUnregisteredMappings(): bool
    {
        return $this->createUnregisteredMappings;
    }
}
