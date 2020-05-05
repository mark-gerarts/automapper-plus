<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Class PropertyAccessor
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
class PropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @inheritdoc
     */
    public function hasProperty($object, string $propertyName): bool
    {
        if (property_exists($object, $propertyName)) {
            return true;
        }

        // property_exists doesn't return true for inherited properties.
        $objectArray = (array) $object;
        foreach ($objectArray as $name => $value) {
            if (substr($name, - \strlen($propertyName) - 1) === "\x00" . $propertyName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getProperty($object, string $propertyName)
    {
        if (isset($object->{$propertyName})) {
            return $object->{$propertyName};
        }

        return $this->getPrivate($object, $propertyName);
    }

    /**
     * @inheritdoc
     */
    public function setProperty($object, string $propertyName, $value): void
    {
        if ($this->isPublic($object, $propertyName)) {
            $object->{$propertyName} = $value;
            return;
        }

        $this->setPrivate($object, $propertyName, $value);
    }

    /**
     * @inheritdoc
     */
    public function getPropertyNames($object): array
    {
        $names = [];
        foreach ($this->getReflectionProperties($object) as $reflectionProperty) {
            $names[] = $reflectionProperty->getName();
        }

        return $names;
    }

    /**
     * Abuses PHP's internal representation of properties when casting an object
     * to an array.
     *
     * @param $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getPrivate($object, string $propertyName)
    {
        $objectArray = (array) $object;
        foreach ($objectArray as $name => $value) {
            if (substr($name, - \strlen($propertyName) - 1) === "\x00" . $propertyName) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param $object
     * @param string $propertyName
     * @param $value
     */
    protected function setPrivate($object, string $propertyName, $value): void
    {
        $property = $this->getReflectionProperty($object, $propertyName);
        if ($property === null) {
            return;
        }

        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * Checks if the given property is public.
     *
     * @param $object
     * @param string $propertyName
     * @return bool
     */
    private function isPublic($object, string $propertyName): bool
    {
        $objectArray = (array) $object;

        return array_key_exists($propertyName, $objectArray);
    }

    /**
     * @param $object
     * @return iterable|\ReflectionProperty[]
     */
    private function getReflectionProperties($object): iterable {
        if ($object === null) {
            return;
        }

        $reflectionClass = new \ReflectionObject($object);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            yield $property;
        }


        // Parent properties are not included in the reflection class, so we'll
        // go up the inheritance chain and collect private properties.
        while ($reflectionClass = $reflectionClass->getParentClass()) {
            foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
                yield $property;
            }
        }
    }

    private function getReflectionProperty($object, string $propertyName): ?\ReflectionProperty {
        foreach ($this->getReflectionProperties($object) as $property) {
            if ($property->getName() === $propertyName) {
                return $property;
            }
        }

        return null;
    }
}
