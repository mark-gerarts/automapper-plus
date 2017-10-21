<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\Exception\InvalidPropertyException;
use AutoMapperPlus\MapperInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\MappingOperation\Reversible;
use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;
use AutoMapperPlus\NameResolver\NameResolverInterface;

/**
 * Class Mapping
 *
 * @package AutoMapperPlus\Configuration
 */
class Mapping implements MappingInterface
{
    /**
     * @var string
     */
    private $sourceClassName;

    /**
     * @var string
     */
    private $destinationClassName;

    /**
     * @var MappingOperationInterface[]
     */
    private $mappingOperations = [];

    /**
     * @var Options
     */
    private $options;

    /**
     * @var AutoMapperConfigInterface
     */
    private $autoMapperConfig;

    /**
     * Mapping constructor.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @param AutoMapperConfigInterface $autoMapperConfig
     */
    public function __construct
    (
        string $sourceClassName,
        string $destinationClassName,
        AutoMapperConfigInterface $autoMapperConfig
    )
    {
        $this->sourceClassName = $sourceClassName;
        $this->destinationClassName = $destinationClassName;
        $this->autoMapperConfig = $autoMapperConfig;

        // Inherit the options from the config.
        $this->options = clone $autoMapperConfig->getOptions();
    }

    /**
     * @inheritdoc
     */
    public function getSourceClassName(): string
    {
        return $this->sourceClassName;
    }

    /**
     * @inheritdoc
     */
    public function getDestinationClassName(): string
    {
        return $this->destinationClassName;
    }

    /**
     * @inheritdoc
     */
    public function forMember
    (
        string $targetPropertyName,
        $operation
    ): MappingInterface
    {
        // If it's just a regular callback, wrap it in an operation.
        if (!$operation instanceof MappingOperationInterface) {
            $operation = Operation::mapFrom($operation);
        }

        // @todo
        // Since we already calculate the source name here, we might be able to
        // add some caching layer.
        $sourcePropertyName = $this->getNameResolver()->getSourcePropertyName(
            $targetPropertyName,
            $operation,
            $this->options
        );

        // Ensure the property exists on the target class before registering it.
        if (!property_exists($this->getSourceClassName(), $sourcePropertyName)) {
            throw InvalidPropertyException::fromNameAndClass(
                $sourcePropertyName,
                $this->getSourceClassName()
            );
        }

        // Make the config available to the operation.
        $operation->setOptions($this->options);

        $this->mappingOperations[$targetPropertyName] = $operation;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reverseMap(array $options = []): MappingInterface
    {
        $reverseMapping = $this->autoMapperConfig->registerMapping(
            $this->getDestinationClassName(),
            $this->getSourceClassName()
        );

        // If there are any naming conventions set, we should reverse those as
        // well for the new mapping.
        if ($this->options->shouldConvertName()) {
            $reverseMapping->withNamingConventions(
                $this->options->getDestinationMemberNamingConvention(),
                $this->options->getSourceMemberNamingConvention()
            );
        }

        // Check if we can reverse any operations for the new mapping.
        foreach ($this->mappingOperations as $originalProperty => $mappingOperation) {
            // We can only define the reverse operation for operations
            // implementing Reversible.
            if (!$mappingOperation instanceof Reversible) {
                continue;
            }

            $reverseTargetProperty = $mappingOperation->getReverseTargetPropertyName(
                $originalProperty,
                $this->getOptions()
            );
            $reverseOperation = $mappingOperation->getReverseOperation(
                $originalProperty,
                $reverseMapping->getOptions()
            );
            $reverseMapping->forMember($reverseTargetProperty, $reverseOperation);
        }

        return $reverseMapping;
    }

    /**
     * @inheritdoc
     */
    public function getMappingOperationFor(string $propertyName): MappingOperationInterface
    {
        return $this->mappingOperations[$propertyName] ?? $this->getDefaultMappingOperation();
    }

    /**
     * @inheritdoc
     */
    public function setDefaults(callable $configurator): MappingInterface
    {
        $configurator($this->options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(): Options
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function skipConstructor(): MappingInterface
    {
        $this->options->skipConstructor();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function dontSkipConstructor(): MappingInterface
    {
        $this->options->dontSkipConstructor();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withNamingConventions
    (
        NamingConventionInterface $sourceNamingConvention,
        NamingConventionInterface $destinationNamingConvention
    ): MappingInterface
    {
        $this->options->setSourceMemberNamingConvention($sourceNamingConvention);
        $this->options->setDestinationMemberNamingConvention($destinationNamingConvention);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withDefaultOperation
    (
        MappingOperationInterface $mappingOperation
    ): MappingInterface
    {
        $this->options->setDefaultMappingOperation($mappingOperation);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withNameResolver
    (
        NameResolverInterface $nameResolver
    ): MappingInterface
    {
        $this->options->setNameResolver($nameResolver);

        return $this;
    }

    /**
     * @return MappingOperationInterface
     */
    protected function getDefaultMappingOperation(): MappingOperationInterface
    {
        $operation = $this->options->getDefaultMappingOperation();
        $operation->setOptions($this->options);

        return $operation;
    }

    /**
     * @return NameResolverInterface
     */
    private function getNameResolver(): NameResolverInterface
    {
        return $this->options->getNameResolver();
    }

    /**
     * @inheritdoc
     */
    public function useCustomMapper(MapperInterface $mapper): void
    {
        $this->options->setCustomMapper($mapper);
    }

    /**
     * @inheritdoc
     */
    public function providesCustomMapper(): bool
    {
        return $this->options->providesCustomMapper();
    }

    /**
     * @inheritdoc
     */
    public function getCustomMapper(): ?MapperInterface
    {
        return $this->options->getCustomMapper();
    }
}
