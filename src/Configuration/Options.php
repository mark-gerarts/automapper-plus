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
     * @var string
     */
    private $objectCrates = [];

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
    public function setSourceMemberNamingConvention
    (
        NamingConventionInterface $sourceMemberNamingConvention
    ): void
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
    public function setDestinationMemberNamingConvention
    (
        NamingConventionInterface $destinationMemberNamingConvention
    ): void
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
    public function setPropertyAccessor
    (
        PropertyAccessorInterface $propertyAccessor
    ): void
    {
        $this->propertyAccessor = $propertyAccessor;
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
    public function setDefaultMappingOperation
    (
        MappingOperationInterface $defaultMappingOperation
    ): void
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
        return !empty($this->sourceMemberNamingConvention)
            && !empty($this->destinationMemberNamingConvention);
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
        return !empty($this->customMapper);
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
}
