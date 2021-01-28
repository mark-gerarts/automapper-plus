<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MappingOperation\ContextAwareOperation;
use AutoMapperPlus\MappingOperation\ContextAwareTrait;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareTrait;
use Traversable;
use function is_array;

/**
 * Class MapTo.
 * Allows a property to be mapped itself.
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapToMultiple extends DefaultMappingOperation implements
    MapperAwareOperation,
    ContextAwareOperation
{
    use MapperAwareTrait;
    use ContextAwareTrait;

    /**
     * @var string
     */
    private $destinationClassList;

    /**
     * @var array
     */
    private $ownContext = [];

    /**
     * MapTo constructor.
     * @param string[] $destinationClassList
     * @param array $context
     *   Indicates whether or not an array as source value should be treated as
     *   a collection of elements, or as an array representing an object.
     * @param array
     *   $context Optional context that will be merged with the parent's
     *   context.
     */
    public function __construct(
        array $destinationClassList,
        array $context = []
    ) {
        $this->destinationClassList = $destinationClassList;
        $this->ownContext = $context;
    }

    /**
     * @return string[]
     */
    public function getDestinationClassList(): array
    {
        return $this->destinationClassList;
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        $value = $this->propertyReader->getProperty(
            $source,
            $this->getSourcePropertyName($propertyName)
        );

        $context = array_merge($this->context, $this->ownContext);
        $returnValue = null;
        $mappingFailed = false;
        foreach ($this->destinationClassList as $destinationClass) {
            if ($this->isCollection($value) === false) {
                try {
                    $returnValue = $this->mapper->map($value, $destinationClass, $context);
                    $mappingFailed = false;
                    break;
                } catch (UnregisteredMappingException $exception) {
                    $mappingFailed = true;
                }
            } else {
                foreach ($value as $item) {
                    if ($this->mapper->getConfiguration()->hasMappingFor(get_class($item), $destinationClass)) {
                        $returnValue[] = $this->mapper->map($item, $destinationClass, $context);
                    }
                }
            }
        }
        if ($mappingFailed) {
            throw UnregisteredMappingException::fromClasses(get_class($value),'"'.implode('", "',$this->destinationClassList).'"');
        }

        return $returnValue;
    }

    /**
     * @param $variable
     * @return bool
     */
    private function isCollection($variable): bool
    {
        return is_array($variable) || $variable instanceof Traversable;
    }
}
