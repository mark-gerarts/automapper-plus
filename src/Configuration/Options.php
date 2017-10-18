<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;
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
    private $shouldSkipConstructor;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var MappingOperationInterface
     */
    private $defaultMappingOperation;

    /**
     * @return Options
     */
    public static function default(): Options
    {
        $config = new static();
        $config->skipConstructor();
        $config->setPropertyAccessor(new PropertyAccessor());
        $config->setDefaultMappingOperation(new DefaultMappingOperation());

        return $config;
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
    )
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
    )
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

    public function skipConstructor()
    {
        $this->shouldSkipConstructor = true;
    }

    public function dontSkipConstructor()
    {
        $this->shouldSkipConstructor = false;
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
    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @return bool
     */
    public function isShouldSkipConstructor(): bool
    {
        return $this->shouldSkipConstructor;
    }

    /**
     * @param bool $shouldSkipConstructor
     */
    public function setShouldSkipConstructor(bool $shouldSkipConstructor)
    {
        $this->shouldSkipConstructor = $shouldSkipConstructor;
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
    )
    {
        $this->defaultMappingOperation = $defaultMappingOperation;
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
}
?>
