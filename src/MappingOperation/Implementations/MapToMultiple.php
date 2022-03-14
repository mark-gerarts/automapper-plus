<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MappingOperation\ContextAwareOperation;
use AutoMapperPlus\MappingOperation\ContextAwareTrait;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareTrait;

/**
 * Class MapToMultiple.
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapToMultiple extends DefaultMappingOperation implements
    MapperAwareOperation,
    ContextAwareOperation
{
    use MapperAwareTrait;
    use ContextAwareTrait;

    /**
     * @var string[]
     */
    private $destinationClassList;

    /**
     * @var array
     */
    private $ownContext;

    /**
     * MapToMultiple constructor.
     *
     * @param string[] $destinationClassList
     *   List of possible destination classes. The first match will be used.
     * @param array $context
     *   Optional context that will be merged with the parent's context.
     */
    public function __construct(
        array $destinationClassList,
        array $context = []
    ) {
        $this->destinationClassList = $destinationClassList;
        $this->ownContext = $context;
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        $context = array_merge($this->context, $this->ownContext);
        $sourceValue = $this->propertyReader->getProperty(
            $source,
            $this->getSourcePropertyName($propertyName)
        );

        if (!$this->isCollection($sourceValue)) {
            return $this->mapSingle($sourceValue, $context);
        }

        return array_map(
            function ($value) use ($context) {
                return $this->mapSingle($value, $context);
            },
            $sourceValue
        );
    }

    private function mapSingle($item, $context)
    {
        $destinationClass = $this->getDestinationClass($item);

        return $this->mapper->map($item, $destinationClass, $context);
    }

    private function getDestinationClass($item): ?string
    {
        foreach ($this->destinationClassList as $destinationClass) {
            $mappingExists = $this->mapper->getConfiguration()->hasMappingFor(
                get_class($item),
                $destinationClass
            );
            if ($mappingExists) {
                return $destinationClass;
            }
        }

        throw UnregisteredMappingException::fromClasses(
            get_class($item),
            ...$this->destinationClassList
        );
    }

    /**
     * @param mixed $variable
     * @return bool
     */
    private function isCollection($variable): bool
    {
        return is_iterable($variable);
    }
}
