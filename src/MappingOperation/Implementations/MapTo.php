<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareTrait;

/**
 * Class MapTo.
 *
 * Allows a property to be mapped itself.
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapTo extends DefaultMappingOperation implements MapperAwareOperation
{
    use MapperAwareTrait;

    /**
     * @var string
     */
    private $destinationClass;

    /**
     * MapTo constructor.
     *
     * @param string $destinationClass
     */
    public function __construct(string $destinationClass)
    {
        $this->destinationClass = $destinationClass;
    }

    /**
     * @return string
     */
    public function getDestinationClass(): string
    {
        return $this->destinationClass;
    }

    /**
     * @inheritdoc
     */
    public function mapProperty(string $propertyName, $source, $destination): void
    {
        if (!$this->canMapProperty($propertyName, $source)) {
            // Alternatively throw an error here.
            return;
        }

        $destinationValue = $this->getPropertyAccessor()->getProperty(
            $destination,
            $this->getSourcePropertyName($propertyName)
        );

        $sourceValue = $this->getSourceValue($source, $propertyName);

        if (!$this->isCollection($sourceValue)) {
            if ($destinationValue instanceof $this->destinationClass) {
                $result = $this->mapper->mapToObject($sourceValue, $destinationValue);
            } else {
                $result = $this->mapper->map($sourceValue, $this->destinationClass);
            }
        } else {
            if ($this->isCollection($destinationValue)) {
                $result = [];
                foreach ($sourceValue as $index => $value) {
                    if (isset($destinationValue[$index])) {
                        $result[] = $this->mapper->mapToObject($value, $destinationValue[$index]);
                    } else {
                        $result[] = $this->mapper->map($value, $this->destinationClass);
                    }

                }
            } else {
                $result = $this->mapper->mapMultiple($sourceValue, $this->destinationClass);
            }
        }

        $this->setDestinationValue($destination, $propertyName, $result);
    }

    /**
     * Checks if the provided input is a collection.
     * @todo: might want to move this outside of this class.
     *
     * @param $variable
     * @return bool
     */
    private function isCollection($variable): bool
    {
        return is_array($variable) || $variable instanceof \Traversable;
    }
}
